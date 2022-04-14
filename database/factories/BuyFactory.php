<?php

namespace Database\Factories;

use App\Models\Bundle;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class BuyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $bundle = Bundle::inRandomOrder()->first();
        return [
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'bundle_id' => $bundle->id,
            'price' => $bundle->price,
            'delivery_cost' => $this->faker->randomElement([0, 0, 5, 8, 10, 12]),
            'paid' => $this->faker->boolean(),
            'issued' => $this->faker->dateTimeBetween('- 3 months', 'now')
        ];
    }
}
