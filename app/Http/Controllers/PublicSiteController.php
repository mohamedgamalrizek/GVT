<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Requests\StoreNewsletterSubscriberRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Developer;
use App\Models\InvestmentThesis;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class PublicSiteController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('public/Home', Cache::remember('public.home', 300, fn (): array => [
            'homeSections' => Page::query()
                ->with('sections')
                ->where('slug', 'home')
                ->first()
                ?->sections
                ->keyBy('key')
                ->toArray() ?? [],
            'featuredTheses' => InvestmentThesis::query()->with('developer:id,name,slug,certification_status')->where('status', 'published')->latest('published_at')->limit(3)->get(),
            'featuredDevelopers' => Developer::query()->whereNotNull('published_at')->latest('rating_score')->limit(4)->get(),
            'featuredPositions' => Position::query()->with('developer:id,name,slug,certification_status')->latest('published_at')->limit(5)->get(),
            'latestInsights' => BlogPost::query()
                ->with('category:id,name,slug')
                ->where('status', 'published')
                ->latest('published_at')
                ->limit(5)
                ->get(),
        ]));
    }

    public function about(): Response
    {
        return Inertia::render('public/About');
    }

    public function methodology(): Response
    {
        return Inertia::render('public/Methodology');
    }

    public function theses(Request $request): Response
    {
        $filters = $request->only(['market', 'city', 'category', 'status', 'search']);
        $theses = InvestmentThesis::query()
            ->with('developer:id,name,slug,certification_status')
            ->when($filters['market'] ?? null, fn ($query, $value) => $query->where('market', $value))
            ->when($filters['city'] ?? null, fn ($query, $value) => $query->where('city', $value))
            ->when($filters['category'] ?? null, fn ($query, $value) => $query->where('category', $value))
            ->when($filters['status'] ?? null, fn ($query, $value) => $query->where('status', $value))
            ->when($filters['search'] ?? null, fn ($query, $value) => $query->where('title', 'like', "%{$value}%"))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('public/Theses/Index', [
            'theses' => $theses,
            'filters' => $filters,
            'filterOptions' => [
                'markets' => InvestmentThesis::query()->distinct()->pluck('market')->values(),
                'cities' => InvestmentThesis::query()->distinct()->pluck('city')->values(),
                'categories' => InvestmentThesis::query()->distinct()->pluck('category')->values(),
                'statuses' => ['published', 'draft', 'watching'],
            ],
        ]);
    }

    public function thesis(InvestmentThesis $thesis): Response
    {
        $thesis->load(['developer.certificationHistories', 'positions.developer']);

        return Inertia::render('public/Theses/Show', ['thesis' => $thesis]);
    }

    public function positions(Request $request): Response
    {
        $filters = $request->only(['city', 'asset_type', 'risk_level', 'certification_status', 'search']);
        $positions = Position::query()
            ->with('developer:id,name,slug,certification_status')
            ->when($filters['city'] ?? null, fn ($query, $value) => $query->where('city', $value))
            ->when($filters['asset_type'] ?? null, fn ($query, $value) => $query->where('asset_type', $value))
            ->when($filters['risk_level'] ?? null, fn ($query, $value) => $query->where('risk_level', $value))
            ->when($filters['certification_status'] ?? null, fn ($query, $value) => $query->where('certification_status', $value))
            ->when($filters['search'] ?? null, fn ($query, $value) => $query->where('project_name', 'like', "%{$value}%"))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('public/Positions/Index', [
            'positions' => $positions,
            'filters' => $filters,
            'filterOptions' => [
                'cities' => Position::query()->distinct()->pluck('city')->values(),
                'assetTypes' => Position::query()->distinct()->pluck('asset_type')->values(),
                'riskLevels' => ['Low', 'Moderate', 'Measured', 'Elevated'],
                'certificationStatuses' => ['Certified', 'Under Review', 'Watchlist', 'Revoked'],
            ],
        ]);
    }

    public function position(Position $position): Response
    {
        $position->load(['developer.certifications', 'developer.certificationHistories', 'thesis']);

        return Inertia::render('public/Positions/Show', ['position' => $position]);
    }

    public function developers(): Response
    {
        return Inertia::render('public/Developers/Index', [
            'developers' => Developer::query()->withCount('positions')->latest('rating_score')->paginate(12),
        ]);
    }

    public function developer(Developer $developer): Response
    {
        $developer->load(['positions.thesis', 'certifications', 'certificationHistories']);

        return Inertia::render('public/Developers/Show', ['developer' => $developer]);
    }

    public function certification(): Response
    {
        return Inertia::render('public/Certification/Index', [
            'statuses' => Developer::query()->selectRaw('certification_status, count(*) as aggregate')->groupBy('certification_status')->pluck('aggregate', 'certification_status'),
            'developers' => Developer::query()->with('certificationHistories')->latest('rating_score')->limit(8)->get(),
        ]);
    }

    public function insights(Request $request): Response
    {
        $posts = BlogPost::query()
            ->with(['category:id,name,slug', 'tags:id,name,slug', 'author:id,name'])
            ->where('status', 'published')
            ->when($request->string('category')->toString(), function ($query, string $category): void {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('public/Insights/Index', [
            'posts' => $posts,
            'categories' => BlogCategory::query()->orderBy('name')->get(['name', 'slug']),
        ]);
    }

    public function insight(BlogPost $post): Response
    {
        $post->load(['category', 'tags', 'author:id,name']);

        return Inertia::render('public/Insights/Show', ['post' => $post]);
    }

    public function newsletter(): Response
    {
        return Inertia::render('public/Newsletter');
    }

    public function subscribe(StoreNewsletterSubscriberRequest $request): RedirectResponse
    {
        NewsletterSubscriber::create([...$request->validated(), 'subscribed_at' => now()]);

        return back()->with('success', 'Subscription recorded. Research updates will follow a measured cadence.');
    }

    public function contact(): Response
    {
        return Inertia::render('public/Contact');
    }

    public function storeContact(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        return back()->with('success', 'Inquiry received. The team will respond with the relevant context.');
    }
}
