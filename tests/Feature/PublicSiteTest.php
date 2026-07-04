<?php

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Developer;
use App\Models\InvestmentThesis;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('public investment pages render with seeded data', function () {
    $this->seed();

    $this->get(route('home'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('public/Home'));
    $this->get(route('theses.index'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('public/Theses/Index'));
    $this->get(route('positions.index'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('public/Positions/Index'));
    $this->get(route('developers.index'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('public/Developers/Index'));
    $this->get(route('certification.index'))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('public/Certification/Index')
        ->has('certificationSections.hero')
    );
    $this->get(route('insights.index'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('public/Insights/Index'));
    $this->get(route('about'))->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('public/About')
        ->has('aboutSections.hero')
        ->where('aboutSections.hero.heading', 'Investment-first real estate advisory.')
    );

    $this->get(route('theses.show', InvestmentThesis::first()->slug))->assertOk();
    $this->get(route('positions.show', Position::first()->slug))->assertOk();
    $this->get(route('developers.show', Developer::first()->slug))->assertOk();
    $this->get(route('insights.show', BlogPost::first()->slug))->assertOk();

    expect(BlogCategory::count())->toBeGreaterThan(0);
    expect(Page::where('slug', 'about')->exists())->toBeTrue();
});

test('newsletter and contact submissions are persisted', function () {
    $this->withoutMiddleware(PreventRequestForgery::class);

    $this->post(route('newsletter.store'), [
        'name' => 'Cycle Investor',
        'email' => 'cycle@example.com',
        'investor_type' => 'Diaspora Investor',
    ])->assertRedirect();

    $this->post(route('contact.store'), [
        'name' => 'Cycle Investor',
        'email' => 'cycle@example.com',
        'company' => 'Private Office',
        'inquiry_type' => 'Investor inquiry',
        'subject' => 'Allocation review',
        'message' => 'We are reviewing portfolio allocation context.',
    ])->assertRedirect();

    expect(NewsletterSubscriber::where('email', 'cycle@example.com')->exists())->toBeTrue();
    expect(ContactMessage::where('email', 'cycle@example.com')->exists())->toBeTrue();
});

test('legacy admin newsletter link redirects to contact inbox', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/newsletter')
        ->assertRedirect('/admin/contact-messages');
});

test('admin certifications link opens certification page editor', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/certifications')
        ->assertRedirect('/admin/page-sections?page=certification');
});
