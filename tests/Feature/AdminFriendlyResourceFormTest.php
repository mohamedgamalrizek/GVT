<?php

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();

    Permission::findOrCreate('manage blog');
    Role::findOrCreate('Editor')->syncPermissions(['manage blog']);
});

test('admin resource pages expose field definitions instead of requiring json editing', function () {
    $user = User::factory()->create();
    $user->assignRole('Editor');

    BlogCategory::create(['name' => 'Research', 'slug' => 'research']);

    $this->actingAs($user)
        ->get(route('admin.resources.index', 'blog'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/ResourceIndex')
            ->has('fields')
            ->where('fields.0.name', 'featured_image')
        );
});

test('resource records can be created and updated from normal form payload arrays', function () {
    $user = User::factory()->create();
    $user->assignRole('Editor');

    $category = BlogCategory::create(['name' => 'Research', 'slug' => 'research']);

    $this->actingAs($user)
        ->post(route('admin.resources.store', 'blog'), [
            'payload' => [
                'blog_category_id' => $category->id,
                'author_id' => $user->id,
                'title' => 'Form Based Insight',
                'slug' => '',
                'excerpt' => 'Short form excerpt.',
                'body' => 'Body created from a normal admin form.',
                'status' => 'published',
                'seo_title' => 'Form Based Insight',
                'seo_description' => 'SEO description.',
                'published_at' => now()->format('Y-m-d H:i:s'),
            ],
        ])
        ->assertRedirect();

    $post = BlogPost::where('title', 'Form Based Insight')->firstOrFail();

    $this->actingAs($user)
        ->put(route('admin.resources.update', ['resource' => 'blog', 'id' => $post->id]), [
            'payload' => [
                ...$post->only(['blog_category_id', 'author_id', 'title', 'slug', 'excerpt', 'body', 'status', 'seo_title', 'seo_description']),
                'title' => 'Updated Form Based Insight',
                'published_at' => now()->format('Y-m-d H:i:s'),
            ],
        ])
        ->assertRedirect();

    expect($post->refresh()->title)->toBe('Updated Form Based Insight');
});

test('blog posts can be created with a featured image from admin forms', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $user->assignRole('Editor');

    $category = BlogCategory::create(['name' => 'Research', 'slug' => 'research']);

    $this->actingAs($user)
        ->post(route('admin.resources.store', 'blog'), [
            'payload' => [
                'blog_category_id' => $category->id,
                'author_id' => $user->id,
                'title' => 'Image Led Insight',
                'slug' => '',
                'excerpt' => 'Short form excerpt.',
                'body' => 'Body created from a normal admin form.',
                'status' => 'published',
                'seo_title' => 'Image Led Insight',
                'seo_description' => 'SEO description.',
                'published_at' => now()->format('Y-m-d H:i:s'),
            ],
            'featured_image' => UploadedFile::fake()->image('insight.jpg', 1400, 900),
        ])
        ->assertRedirect();

    $post = BlogPost::where('title', 'Image Led Insight')->firstOrFail();

    expect($post->featured_image_path)->toStartWith('blog/');
    Storage::disk('public')->assertExists($post->featured_image_path);

    $this->actingAs($user)
        ->get(route('insights.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/Insights/Index')
            ->where('posts.data.0.featured_image_path', $post->featured_image_path)
        );
});
