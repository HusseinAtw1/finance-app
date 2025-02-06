<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'     => 1,
            'account_id'  => 1,
            'type'        => $this->faker->randomElement(['credit', 'debit']),
            'amount'      => $this->faker->randomFloat(2, 1, 1000),
            'description' => $this->faker->sentence,
            'created_at'  => now(),
            'updated_at'  => now(),

        ];
    }
}
