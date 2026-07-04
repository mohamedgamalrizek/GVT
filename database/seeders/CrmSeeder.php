<?php

namespace Database\Seeders;

use App\Models\CrmClient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CrmSeeder extends Seeder
{
    public function run(): void
    {
        Permission::findOrCreate('manage crm');
        Permission::findOrCreate('manage assigned clients');
        Permission::findOrCreate('view dashboard');

        Role::findOrCreate('Sales')->syncPermissions(['view dashboard', 'manage assigned clients']);

        Role::query()
            ->whereIn('name', ['Super Admin', 'Admin'])
            ->get()
            ->each(fn (Role $role) => $role->givePermissionTo(['manage crm', 'manage assigned clients']));

        $admin = User::query()->where('email', 'admin@gvt.test')->first()
            ?? User::factory()->create(['email' => 'admin@gvt.test', 'password' => Hash::make('password')]);

        $salesUsers = collect([
            ['name' => 'Youssef Adel', 'email' => 'sales@gvt.test'],
            ['name' => 'Laila Mansour', 'email' => 'sales.two@gvt.test'],
        ])->map(function (array $sales): User {
            $user = User::updateOrCreate(
                ['email' => $sales['email']],
                ['name' => $sales['name'], 'password' => Hash::make('password'), 'email_verified_at' => now()],
            );

            $user->assignRole('Sales');

            return $user;
        });

        collect($this->clients())->each(function (array $client, int $index) use ($admin, $salesUsers): void {
            $model = CrmClient::updateOrCreate(
                ['email' => $client['email']],
                [
                    'assigned_to_user_id' => $salesUsers[$index % $salesUsers->count()]->id,
                    'created_by_user_id' => $admin->id,
                    'name' => $client['name'],
                    'phone' => $client['phone'],
                    'company' => $client['company'],
                    'investor_type' => $client['investor_type'],
                    'status' => $client['status'],
                    'priority' => $client['priority'],
                    'source' => $client['source'],
                    'budget_range' => $client['budget_range'],
                    'preferred_market' => $client['preferred_market'],
                    'notes' => $client['notes'],
                    'last_contacted_at' => now()->subDays($index + 1),
                    'next_follow_up_at' => now()->addDays($index + 2),
                ],
            );

            $model->updates()->updateOrCreate(
                ['type' => 'call', 'body' => $client['update']],
                [
                    'user_id' => $model->assigned_to_user_id,
                    'status_to' => $model->status,
                    'contacted_at' => now()->subDays($index + 1),
                    'next_follow_up_at' => $model->next_follow_up_at,
                ],
            );
        });
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function clients(): array
    {
        return [
            [
                'name' => 'Hassan Makram',
                'email' => 'hassan@example.com',
                'phone' => '+20 100 555 0101',
                'company' => 'Private Office',
                'investor_type' => 'Egyptian HNWI',
                'status' => 'qualified',
                'priority' => 'high',
                'source' => 'Website inquiry',
                'budget_range' => '$250k-$400k',
                'preferred_market' => 'North Coast',
                'notes' => 'Interested in income-backed hospitality exposure with clear developer certification.',
                'update' => 'Initial qualification completed. Investor requested thesis context and risk notes.',
            ],
            [
                'name' => 'Dina Samir',
                'email' => 'dina@example.com',
                'phone' => '+971 50 555 0102',
                'company' => null,
                'investor_type' => 'Diaspora Investor',
                'status' => 'contacted',
                'priority' => 'medium',
                'source' => 'Referral',
                'budget_range' => '$150k-$250k',
                'preferred_market' => 'New Cairo',
                'notes' => 'Requires currency context and rental demand evidence before allocation.',
                'update' => 'Follow-up call completed. Shared New Cairo education corridor positioning.',
            ],
            [
                'name' => 'Karim El Sayed',
                'email' => 'karim@example.com',
                'phone' => '+20 122 555 0103',
                'company' => 'Family Capital',
                'investor_type' => 'Family Office',
                'status' => 'proposal',
                'priority' => 'urgent',
                'source' => 'Investor event',
                'budget_range' => '$500k-$750k',
                'preferred_market' => 'Red Sea',
                'notes' => 'Looking for portfolio allocation across hard-currency demand corridors.',
                'update' => 'Proposal requested for certified developer basket and exit assumptions.',
            ],
            [
                'name' => 'Mona Fathy',
                'email' => 'mona@example.com',
                'phone' => '+966 55 555 0104',
                'company' => null,
                'investor_type' => 'Diaspora Investor',
                'status' => 'new',
                'priority' => 'medium',
                'source' => 'Newsletter',
                'budget_range' => '$100k-$180k',
                'preferred_market' => 'West Cairo',
                'notes' => 'Needs introductory advisory call and market-cycle explanation.',
                'update' => 'Client added from newsletter response. Qualification pending.',
            ],
        ];
    }
}
