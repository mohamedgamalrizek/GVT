<?php

use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

test('admin can update a page section and upload a section image', function () {
    Storage::fake('public');

    Permission::findOrCreate('manage pages');
    $user = User::factory()->create();
    $user->givePermissionTo('manage pages');

    $page = Page::create([
        'title' => 'Home',
        'slug' => 'home',
        'template' => 'home',
        'status' => 'published',
    ]);

    $section = PageSection::create([
        'page_id' => $page->id,
        'key' => 'hero',
        'eyebrow' => 'Old',
        'heading' => 'Old heading',
        'body' => 'Old body',
        'content' => ['primary_cta' => 'Explore'],
        'sort_order' => 1,
    ]);

    $this->actingAs($user)
        ->put(route('admin.page-sections.update', $section), [
            'eyebrow' => 'Hero',
            'heading' => 'New heading',
            'body' => 'New body',
            'content' => json_encode(['primary_cta' => 'Review Theses']),
        ])
        ->assertRedirect();

    $this->actingAs($user)
        ->post(route('admin.page-sections.image', $section), [
            'image' => UploadedFile::fake()->image('hero.jpg', 1600, 900),
        ])
        ->assertRedirect();

    $section->refresh();

    expect($section->heading)->toBe('New heading');
    expect($section->content['primary_cta'])->toBe('Review Theses');
    expect($section->content['image_path'])->toStartWith('page-sections/');
    Storage::disk('public')->assertExists($section->content['image_path']);
});
