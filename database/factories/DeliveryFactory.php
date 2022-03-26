<?php

namespace Database\Factories;

use App\Models\DeliveryService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $distance = $this->faker->numberBetween(-1,40);
        return [
            'delivery_service_id' => DeliveryService::inRandomOrder()->first()->id,
            'date' => now()->addDays($distance),
            'deadline' => now()->addDays($distance - 2)
        ];
    }
}
