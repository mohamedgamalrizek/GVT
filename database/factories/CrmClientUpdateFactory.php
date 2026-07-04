<?php

namespace Database\Factories;

use App\Models\CrmClient;
use App\Models\CrmClientUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmClientUpdate>
 */
class CrmClientUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crm_client_id' => CrmClient::factory(),
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['note', 'call', 'email', 'meeting', 'whatsapp']),
            'status_from' => fake()->optional()->randomElement(['new', 'qualified', 'contacted', 'proposal']),
            'status_to' => fake()->optional()->randomElement(['qualified', 'contacted', 'proposal', 'negotiation', 'won']),
            'body' => fake()->paragraph(),
            'contacted_at' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
            'next_follow_up_at' => fake()->optional()->dateTimeBetween('now', '+30 days'),
        ];
    }
}
