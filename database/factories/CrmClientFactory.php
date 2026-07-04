<?php

namespace Database\Factories;

use App\Models\CrmClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmClient>
 */
class CrmClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assigned_to_user_id' => User::factory(),
            'created_by_user_id' => User::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'company' => fake()->optional()->company(),
            'investor_type' => fake()->randomElement(['Egyptian HNWI', 'Diaspora Investor', 'Family Office', 'Developer']),
            'status' => fake()->randomElement(['new', 'qualified', 'contacted', 'proposal', 'negotiation', 'won', 'lost', 'on_hold']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'source' => fake()->randomElement(['Website inquiry', 'Referral', 'Newsletter', 'Investor event']),
            'budget_range' => fake()->randomElement(['$100k-$180k', '$150k-$250k', '$250k-$400k', '$500k-$750k']),
            'preferred_market' => fake()->randomElement(['North Coast', 'New Cairo', 'Red Sea', 'West Cairo']),
            'notes' => fake()->paragraph(),
            'last_contacted_at' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
            'next_follow_up_at' => fake()->optional()->dateTimeBetween('now', '+30 days'),
        ];
    }
}
