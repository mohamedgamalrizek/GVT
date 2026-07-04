<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\CrmClient;
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
        $user = auth()->user();
        abort_unless($user?->can('view dashboard'), 403);

        if (! $user->can('manage crm') && $user->can('manage assigned clients')) {
            return Inertia::render('admin/Dashboard', [
                'stats' => [
                    'assignedClients' => CrmClient::query()->where('assigned_to_user_id', $user->id)->count(),
                    'openClients' => CrmClient::query()->where('assigned_to_user_id', $user->id)->whereNotIn('status', ['won', 'lost'])->count(),
                    'wonClients' => CrmClient::query()->where('assigned_to_user_id', $user->id)->where('status', 'won')->count(),
                    'followUps' => CrmClient::query()->where('assigned_to_user_id', $user->id)->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now()->addDays(7))->count(),
                ],
                'messages' => [],
                'positions' => [],
            ]);
        }

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
