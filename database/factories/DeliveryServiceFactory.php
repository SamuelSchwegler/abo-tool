<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryService>
 */
class DeliveryServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => Str::random(),
            'days' => $this->faker->randomElements(['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']),
        ];
    }
}
