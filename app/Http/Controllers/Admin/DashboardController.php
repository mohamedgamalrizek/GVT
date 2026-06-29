<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Developer;
use App\Models\InvestmentThesis;
use App\Models\NewsletterSubscriber;
use App\Models\Position;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        abort_unless(auth()->user()?->can('view dashboard'), 403);

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'developers' => Developer::query()->count(),
                'positions' => Position::query()->count(),
                'theses' => InvestmentThesis::query()->count(),
                'posts' => BlogPost::query()->count(),
                'subscribers' => NewsletterSubscriber::query()->count(),
                'newMessages' => ContactMessage::query()->where('status', 'new')->count(),
            ],
            'messages' => ContactMessage::query()->latest()->limit(5)->get(),
            'positions' => Position::query()->with('developer:id,name')->latest()->limit(5)->get(),
        ]);
    }
}
