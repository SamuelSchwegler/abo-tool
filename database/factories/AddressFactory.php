<?php

namespace Database\Factories;

use App\Models\Postcode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $postcode_options = [
            $this->faker->postcode(),
            Postcode::inRandomOrder()->first()->postcode,
            Postcode::inRandomOrder()->first()->postcode,
            Postcode::inRandomOrder()->first()->postcode,
            Postcode::inRandomOrder()->first()->postcode,
            Postcode::inRandomOrder()->first()->postcode,
        ];

        return [
            'street' => $this->faker->streetAddress(),
            'postcode' => $this->faker->randomElement($postcode_options),
            'city' => $this->faker->city(),
        ];
    }
}
