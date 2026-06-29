<?php

use App\Models\InvestmentThesis;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

test('admin can upload a featured image for an investment thesis', function () {
    Storage::fake('public');

    Permission::findOrCreate('manage theses');
    $user = User::factory()->create();
    $user->givePermissionTo('manage theses');

    $thesis = InvestmentThesis::create([
        'title' => 'Evidence-Led Thesis',
        'slug' => 'evidence-led-thesis',
        'market' => 'Egypt',
        'city' => 'Cairo',
        'category' => 'Residential',
        'status' => 'published',
        'executive_summary' => 'Executive summary.',
        'market_context' => 'Market context.',
        'asset_rationale' => 'Asset rationale.',
        'risk_notes' => 'Risk notes.',
        'published_at' => now(),
    ]);

    $this->actingAs($user)
        ->post(route('admin.theses.featured-image', $thesis), [
            'featured_image' => UploadedFile::fake()->image('research.jpg', 1200, 800),
        ])
        ->assertRedirect();

    $thesis->refresh();

    expect($thesis->featured_image_path)->toStartWith('theses/');
    Storage::disk('public')->assertExists($thesis->featured_image_path);
});
