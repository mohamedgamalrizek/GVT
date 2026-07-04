<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCrmClientRequest;
use App\Http\Requests\Admin\StoreCrmClientUpdateRequest;
use App\Http\Requests\Admin\UpdateCrmClientRequest;
use App\Models\CrmClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CrmClientController extends Controller
{
    public function index(Request $request, ?CrmClient $crmClient = null): Response
    {
        $user = $request->user();
        abort_unless($user?->can('viewAny', CrmClient::class), 403);

        if ($crmClient !== null) {
            Gate::authorize('view', $crmClient);
        }

        $clientsQuery = $this->scopedClients($user)
            ->with(['assignedTo:id,name,email', 'createdBy:id,name,email'])
            ->when($request->string('search')->toString(), function (Builder $query, string $search): void {
                $query->where(function (Builder $builder) use ($search): void {
                    $builder
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->when($request->string('status')->toString(), fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($request->string('priority')->toString(), fn (Builder $query, string $priority) => $query->where('priority', $priority))
            ->latest('updated_at');

        return Inertia::render('admin/Crm/Index', [
            'clients' => $clientsQuery->paginate(12)->withQueryString(),
            'selectedClient' => $crmClient?->load([
                'assignedTo:id,name,email',
                'createdBy:id,name,email',
                'updates.user:id,name,email',
            ]),
            'salesUsers' => $this->salesUsers(),
            'filters' => $request->only(['search', 'status', 'priority']),
            'statusOptions' => $this->statusOptions(),
            'priorityOptions' => $this->priorityOptions(),
            'canManageCrm' => $user->can('manage crm'),
            'stats' => [
                'total' => (clone $this->scopedClients($user))->count(),
                'open' => (clone $this->scopedClients($user))->whereNotIn('status', ['won', 'lost'])->count(),
                'won' => (clone $this->scopedClients($user))->where('status', 'won')->count(),
                'followUps' => (clone $this->scopedClients($user))->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now()->addDays(7))->count(),
            ],
        ]);
    }

    public function store(StoreCrmClientRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $user = $request->user();

        if (! $user->can('manage crm')) {
            $payload['assigned_to_user_id'] = $user->id;
        }

        $client = CrmClient::query()->create([
            ...$payload,
            'created_by_user_id' => $user->id,
        ]);

        $client->updates()->create([
            'user_id' => $user->id,
            'type' => 'note',
            'status_to' => $client->status,
            'body' => 'Client profile created.',
            'next_follow_up_at' => $client->next_follow_up_at,
        ]);

        return redirect()->route('admin.crm.show', $client)->with('success', 'Client created.');
    }

    public function update(UpdateCrmClientRequest $request, CrmClient $crmClient): RedirectResponse
    {
        $payload = $request->validated();
        $user = $request->user();
        $oldStatus = $crmClient->status;
        $oldAssignee = $crmClient->assigned_to_user_id;

        if (! $user->can('assign', $crmClient)) {
            unset($payload['assigned_to_user_id']);
        }

        $crmClient->update($payload);

        if ($oldStatus !== $crmClient->status || $oldAssignee !== $crmClient->assigned_to_user_id) {
            $crmClient->updates()->create([
                'user_id' => $user->id,
                'type' => 'status_change',
                'status_from' => $oldStatus,
                'status_to' => $crmClient->status,
                'body' => $oldAssignee !== $crmClient->assigned_to_user_id
                    ? 'Client assignment updated.'
                    : 'Client status updated.',
                'next_follow_up_at' => $crmClient->next_follow_up_at,
            ]);
        }

        return back()->with('success', 'Client updated.');
    }

    public function storeUpdate(StoreCrmClientUpdateRequest $request, CrmClient $crmClient): RedirectResponse
    {
        $payload = $request->validated();
        $oldStatus = $crmClient->status;

        $crmClient->updates()->create([
            ...$payload,
            'user_id' => $request->user()->id,
            'status_from' => $oldStatus,
        ]);

        $crmClient->update([
            'status' => $payload['status_to'] ?? $crmClient->status,
            'last_contacted_at' => $payload['contacted_at'] ?? now(),
            'next_follow_up_at' => $payload['next_follow_up_at'] ?? $crmClient->next_follow_up_at,
        ]);

        return back()->with('success', 'Client update added.');
    }

    public function destroy(Request $request, CrmClient $crmClient): RedirectResponse
    {
        Gate::authorize('delete', $crmClient);

        $crmClient->delete();

        return redirect()->route('admin.crm.index')->with('success', 'Client deleted.');
    }

    private function scopedClients(User $user): Builder
    {
        return CrmClient::query()
            ->when(! $user->can('manage crm'), fn (Builder $query) => $query->where('assigned_to_user_id', $user->id));
    }

    /**
     * @return array<int, array{id: int, name: string, email: string}>
     */
    private function salesUsers(): array
    {
        return User::query()
            ->permission('manage assigned clients')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->toArray();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return [
            ['value' => 'new', 'label' => 'New'],
            ['value' => 'qualified', 'label' => 'Qualified'],
            ['value' => 'contacted', 'label' => 'Contacted'],
            ['value' => 'proposal', 'label' => 'Proposal'],
            ['value' => 'negotiation', 'label' => 'Negotiation'],
            ['value' => 'won', 'label' => 'Won'],
            ['value' => 'lost', 'label' => 'Lost'],
            ['value' => 'on_hold', 'label' => 'On Hold'],
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function priorityOptions(): array
    {
        return [
            ['value' => 'low', 'label' => 'Low'],
            ['value' => 'medium', 'label' => 'Medium'],
            ['value' => 'high', 'label' => 'High'],
            ['value' => 'urgent', 'label' => 'Urgent'],
        ];
    }
}
