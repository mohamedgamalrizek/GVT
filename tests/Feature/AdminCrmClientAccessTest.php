<?php

use App\Models\CrmClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->withoutVite();

    Permission::findOrCreate('view dashboard');
    Permission::findOrCreate('manage crm');
    Permission::findOrCreate('manage assigned clients');

    Role::findOrCreate('Super Admin')->syncPermissions(['view dashboard', 'manage crm', 'manage assigned clients']);
    Role::findOrCreate('Sales')->syncPermissions(['view dashboard', 'manage assigned clients']);
});

test('admin can see all crm clients and reassign clients', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $firstSales = User::factory()->create();
    $firstSales->assignRole('Sales');
    $secondSales = User::factory()->create();
    $secondSales->assignRole('Sales');

    $client = CrmClient::factory()->create(['assigned_to_user_id' => $firstSales->id]);
    CrmClient::factory()->create(['assigned_to_user_id' => $secondSales->id]);

    $this->actingAs($admin)
        ->get(route('admin.crm.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Crm/Index')
            ->has('clients.data', 2)
            ->where('canManageCrm', true)
        );

    $this->actingAs($admin)
        ->put(route('admin.crm.update', $client), [
            ...$client->only([
                'name',
                'email',
                'phone',
                'company',
                'investor_type',
                'status',
                'priority',
                'source',
                'budget_range',
                'preferred_market',
                'notes',
            ]),
            'assigned_to_user_id' => $secondSales->id,
            'next_follow_up_at' => null,
        ])
        ->assertRedirect();

    expect($client->refresh()->assigned_to_user_id)->toBe($secondSales->id);
});

test('sales users only see and update their assigned clients', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');
    $otherSales = User::factory()->create();
    $otherSales->assignRole('Sales');

    $ownClient = CrmClient::factory()->create(['assigned_to_user_id' => $sales->id, 'name' => 'Visible Client']);
    $otherClient = CrmClient::factory()->create(['assigned_to_user_id' => $otherSales->id, 'name' => 'Hidden Client']);

    $this->actingAs($sales)
        ->get(route('admin.crm.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/Crm/Index')
            ->has('clients.data', 1)
            ->where('clients.data.0.id', $ownClient->id)
            ->where('canManageCrm', false)
        );

    $this->actingAs($sales)->get(route('admin.crm.show', $otherClient))->assertForbidden();

    $this->actingAs($sales)
        ->post(route('admin.crm.updates.store', $ownClient), [
            'type' => 'call',
            'body' => 'Qualified investor and scheduled next review.',
            'status_to' => 'qualified',
            'contacted_at' => now()->toDateTimeString(),
            'next_follow_up_at' => now()->addDay()->toDateTimeString(),
        ])
        ->assertRedirect();

    expect($ownClient->refresh()->status)->toBe('qualified');
    expect($ownClient->updates()->count())->toBe(1);
});

test('sales-created clients are assigned to the creating sales user', function () {
    $sales = User::factory()->create();
    $sales->assignRole('Sales');
    $otherSales = User::factory()->create();
    $otherSales->assignRole('Sales');

    $this->actingAs($sales)
        ->post(route('admin.crm.store'), [
            'assigned_to_user_id' => $otherSales->id,
            'name' => 'New Investor',
            'email' => 'new-investor@example.com',
            'phone' => '+20 100 000 0000',
            'company' => null,
            'investor_type' => 'Diaspora Investor',
            'status' => 'new',
            'priority' => 'medium',
            'source' => 'Referral',
            'budget_range' => '$150k-$250k',
            'preferred_market' => 'New Cairo',
            'notes' => 'Needs qualification.',
            'next_follow_up_at' => null,
        ])
        ->assertRedirect();

    expect(CrmClient::where('email', 'new-investor@example.com')->first()?->assigned_to_user_id)->toBe($sales->id);
});
