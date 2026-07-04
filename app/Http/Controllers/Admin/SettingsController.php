<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWebsiteSettingsRequest;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function edit(): Response
    {
        abort_unless(auth()->user()?->can('manage settings'), 403);

        return Inertia::render('admin/Settings', [
            'settings' => WebsiteSetting::site(),
        ]);
    }

    public function update(UpdateWebsiteSettingsRequest $request): RedirectResponse
    {
        $settings = Arr::except($request->validated(), ['logo', 'favicon']);
        $current = WebsiteSetting::site();

        if ($request->hasFile('logo')) {
            if ($current['logo_path']) {
                Storage::disk('public')->delete($current['logo_path']);
            }

            $settings['logo_path'] = $request->file('logo')->store('brand', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($current['favicon_path']) {
                Storage::disk('public')->delete($current['favicon_path']);
            }

            $settings['favicon_path'] = $request->file('favicon')->store('brand', 'public');
        }

        WebsiteSetting::saveSite($settings);

        return back()->with('success', 'Website settings updated.');
    }
}
