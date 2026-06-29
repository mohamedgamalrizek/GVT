<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveResourceRequest;
use App\Http\Requests\Admin\UploadThesisImageRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\Certification;
use App\Models\CertificationHistory;
use App\Models\ContactMessage;
use App\Models\Developer;
use App\Models\InvestmentThesis;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Position;
use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class ResourceController extends Controller
{
    /**
     * @var array<string, array{model: class-string<Model>, permission: string, title: string, relations?: array<int, string>}>
     */
    private array $resources = [
        'pages' => ['model' => Page::class, 'permission' => 'manage pages', 'title' => 'Pages', 'relations' => ['sections']],
        'page-sections' => ['model' => PageSection::class, 'permission' => 'manage pages', 'title' => 'Page Sections', 'relations' => ['page:id,title,slug']],
        'theses' => ['model' => InvestmentThesis::class, 'permission' => 'manage theses', 'title' => 'Investment Theses', 'relations' => ['developer:id,name']],
        'positions' => ['model' => Position::class, 'permission' => 'manage positions', 'title' => 'Positions', 'relations' => ['developer:id,name', 'thesis:id,title']],
        'developers' => ['model' => Developer::class, 'permission' => 'manage developers', 'title' => 'Developers'],
        'certifications' => ['model' => Certification::class, 'permission' => 'manage certifications', 'title' => 'Certifications', 'relations' => ['developer:id,name']],
        'certification-history' => ['model' => CertificationHistory::class, 'permission' => 'manage certifications', 'title' => 'Certification History', 'relations' => ['developer:id,name']],
        'blog' => ['model' => BlogPost::class, 'permission' => 'manage blog', 'title' => 'Blog Posts', 'relations' => ['category:id,name', 'tags:id,name']],
        'categories' => ['model' => BlogCategory::class, 'permission' => 'manage blog', 'title' => 'Categories'],
        'tags' => ['model' => BlogTag::class, 'permission' => 'manage blog', 'title' => 'Tags'],
        'newsletter' => ['model' => NewsletterSubscriber::class, 'permission' => 'export subscribers', 'title' => 'Newsletter Subscribers'],
        'contact-messages' => ['model' => ContactMessage::class, 'permission' => 'view inquiries', 'title' => 'Contact Messages'],
        'users' => ['model' => User::class, 'permission' => 'manage users', 'title' => 'Users', 'relations' => ['roles:id,name']],
        'roles' => ['model' => Role::class, 'permission' => 'manage roles', 'title' => 'Roles', 'relations' => ['permissions:id,name']],
        'settings' => ['model' => WebsiteSetting::class, 'permission' => 'manage settings', 'title' => 'Website Settings'],
    ];

    public function index(Request $request, string $resource): Response
    {
        $config = $this->config($resource);
        abort_unless($request->user()?->can($config['permission']), 403);

        $query = $config['model']::query();

        if ($relations = $config['relations'] ?? []) {
            $query->with($relations);
        }

        if ($search = $request->string('search')->toString()) {
            $table = (new $config['model'])->getTable();
            $columns = collect(['name', 'title', 'project_name', 'email', 'key', 'subject'])
                ->filter(fn (string $column): bool => Schema::hasColumn($table, $column));

            if ($columns->isNotEmpty()) {
                $query->where(function ($builder) use ($columns, $search): void {
                    foreach ($columns as $column) {
                        $builder->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }
        }

        return Inertia::render('admin/ResourceIndex', [
            'resource' => $resource,
            'title' => $config['title'],
            'items' => $query->latest('id')->paginate(15)->withQueryString(),
            'meta' => $this->meta(),
        ]);
    }

    public function store(SaveResourceRequest $request, string $resource): RedirectResponse
    {
        $config = $this->config($resource);
        abort_unless($request->user()?->can($config['permission']), 403);
        $payload = $this->preparePayload($resource, $this->payload($request));

        $config['model']::query()->create($payload);

        return back()->with('success', "{$config['title']} record created.");
    }

    public function update(SaveResourceRequest $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        abort_unless($request->user()?->can($config['permission']), 403);
        $model = $config['model']::query()->findOrFail($id);
        $model->update($this->preparePayload($resource, $this->payload($request)));

        return back()->with('success', "{$config['title']} record updated.");
    }

    public function destroy(Request $request, string $resource, int $id): RedirectResponse
    {
        $config = $this->config($resource);
        abort_unless($request->user()?->can($config['permission']), 403);
        $config['model']::query()->findOrFail($id)->delete();

        return back()->with('success', "{$config['title']} record deleted.");
    }

    public function uploadThesisImage(UploadThesisImageRequest $request, InvestmentThesis $thesis): RedirectResponse
    {
        if ($thesis->featured_image_path) {
            Storage::disk('public')->delete($thesis->featured_image_path);
        }

        $path = $request->file('featured_image')->store('theses', 'public');

        $thesis->update(['featured_image_path' => $path]);

        return back()->with('success', 'Thesis image updated.');
    }

    /**
     * @return array{model: class-string<Model>, permission: string, title: string, relations?: array<int, string>}
     */
    private function config(string $resource): array
    {
        abort_unless(array_key_exists($resource, $this->resources), 404);

        return $this->resources[$resource];
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(SaveResourceRequest $request): array
    {
        $payload = $request->input('payload');

        if (is_string($payload)) {
            $decoded = json_decode($payload, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($payload) ? $payload : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function preparePayload(string $resource, array $payload): array
    {
        $payload = Arr::except($payload, ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at', 'roles', 'permissions']);

        if (isset($payload['name']) && ! isset($payload['slug']) && in_array($resource, ['developers', 'categories', 'tags'], true)) {
            $payload['slug'] = Str::slug($payload['name']);
        }

        if (isset($payload['title']) && ! isset($payload['slug'])) {
            $payload['slug'] = Str::slug($payload['title']);
        }

        if (isset($payload['project_name']) && ! isset($payload['slug'])) {
            $payload['slug'] = Str::slug($payload['project_name']);
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(): array
    {
        return [
            'developers' => Developer::query()->orderBy('name')->get(['id', 'name']),
            'theses' => InvestmentThesis::query()->orderBy('title')->get(['id', 'title']),
            'categories' => BlogCategory::query()->orderBy('name')->get(['id', 'name']),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ];
    }
}
