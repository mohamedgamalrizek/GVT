<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePageSectionRequest;
use App\Http\Requests\Admin\UploadPageSectionImageRequest;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PageSectionController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless(auth()->user()?->can('manage pages'), 403);

        return Inertia::render('admin/PageSections', [
            'selectedPageSlug' => $request->string('page')->toString(),
            'pages' => Page::query()
                ->with('sections')
                ->orderByRaw("case when slug = 'home' then 0 else 1 end")
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function update(UpdatePageSectionRequest $request, PageSection $section): RedirectResponse
    {
        $section->loadMissing('page');

        $content = $section->content ?? [];
        $incomingContent = $request->string('content')->toString();

        if ($incomingContent !== '') {
            $decoded = json_decode($incomingContent, true);
            $content = is_array($decoded) ? [...$content, ...$decoded] : $content;
        }

        $content = [...$content, ...$this->contentPayload($request->input('content_payload', []))];

        $section->update([
            'eyebrow' => $request->string('eyebrow')->toString() ?: null,
            'heading' => $request->string('heading')->toString(),
            'body' => $request->string('body')->toString() ?: null,
            'content' => $content,
        ]);

        Cache::forget('public.home');
        Cache::forget('public.'.$section->page?->slug);

        return back()->with('success', 'Section updated.');
    }

    public function uploadImage(UploadPageSectionImageRequest $request, PageSection $section): RedirectResponse
    {
        $section->loadMissing('page');

        $content = $section->content ?? [];

        if (($content['image_path'] ?? null) !== null) {
            Storage::disk('public')->delete($content['image_path']);
        }

        $content['image_path'] = $request->file('image')->store('page-sections', 'public');
        $section->update(['content' => $content]);

        Cache::forget('public.home');
        Cache::forget('public.'.$section->page?->slug);

        return back()->with('success', 'Section image updated.');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function contentPayload(array $payload): array
    {
        $content = [];

        foreach (['primary_cta', 'secondary_cta', 'bio'] as $key) {
            if (array_key_exists($key, $payload)) {
                $content[$key] = blank($payload[$key]) ? null : $payload[$key];
            }
        }

        if (isset($payload['steps_text'])) {
            $content['steps'] = $this->keyValueLines((string) $payload['steps_text']);
        }

        if (isset($payload['signals_text'])) {
            $content['signals'] = collect($this->lines((string) $payload['signals_text']))
                ->map(function (string $line): array {
                    [$label, $title, $value, $meta, $change] = array_pad(array_map('trim', explode('|', $line)), 5, '');

                    return compact('label', 'title', 'value', 'meta', 'change');
                })
                ->filter(fn (array $signal): bool => filled($signal['title']) || filled($signal['label']))
                ->values()
                ->all();
        }

        if (isset($payload['stats_text'])) {
            $content['stats'] = collect($this->lines((string) $payload['stats_text']))
                ->map(function (string $line): array {
                    [$value, $label] = array_pad(array_map('trim', explode('|', $line)), 2, '');

                    return compact('value', 'label');
                })
                ->filter(fn (array $stat): bool => filled($stat['value']) || filled($stat['label']))
                ->values()
                ->all();
        }

        if (isset($payload['principles_text'])) {
            $content['principles'] = $this->lines((string) $payload['principles_text']);
        }

        if (isset($payload['items_text'])) {
            $content['items'] = collect($this->lines((string) $payload['items_text']))
                ->map(function (string $line): array {
                    [$title, $body] = array_pad(array_map('trim', explode('|', $line)), 2, '');

                    return compact('title', 'body');
                })
                ->filter(fn (array $item): bool => filled($item['title']) || filled($item['body']))
                ->values()
                ->all();
        }

        return $content;
    }

    /**
     * @return array<int, string>
     */
    private function lines(string $value): array
    {
        return collect(explode("\n", $value))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function keyValueLines(string $value): array
    {
        return collect($this->lines($value))
            ->mapWithKeys(function (string $line): array {
                [$key, $body] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');

                return filled($key) ? [$key => $body] : [];
            })
            ->all();
    }
}
