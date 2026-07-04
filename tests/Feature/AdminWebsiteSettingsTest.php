<?php

use App\Models\User;
use App\Models\WebsiteSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

test('admin can update website settings and upload brand assets', function () {
    Storage::fake('public');

    Permission::findOrCreate('manage settings');
    $user = User::factory()->create();
    $user->givePermissionTo('manage settings');

    $this->actingAs($user)
        ->post('/admin/settings', [
            '_method' => 'put',
            'brand_name' => 'GVT Intelligence',
            'short_name' => 'GVT',
            'slogan' => 'Every investment needs a thesis.',
            'contact_email' => 'desk@gvt.test',
            'contact_phone' => '+20 111 111 1111',
            'contact_address' => 'Cairo, Egypt',
            'linkedin_url' => 'https://linkedin.com/company/gvt',
            'x_url' => null,
            'instagram_url' => null,
            'default_seo_title' => 'GVT Intelligence',
            'default_seo_description' => 'Evidence-led real estate investment advisory.',
            'default_seo_keywords' => 'real estate, investment thesis',
            'logo' => UploadedFile::fake()->image('logo.png', 400, 160),
            'favicon' => UploadedFile::fake()->image('favicon.png', 64, 64),
        ])
        ->assertRedirect();

    $settings = WebsiteSetting::site();

    expect($settings['brand_name'])->toBe('GVT Intelligence');
    expect($settings['contact_email'])->toBe('desk@gvt.test');
    expect($settings['logo_path'])->toStartWith('brand/');
    expect($settings['favicon_path'])->toStartWith('brand/');

    Storage::disk('public')->assertExists($settings['logo_path']);
    Storage::disk('public')->assertExists($settings['favicon_path']);
});
