<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $billing = Address::inRandomOrder()->first()->id;
        $delivery = $this->faker->randomElement([$billing, null, Address::inRandomOrder()->first()->id]);

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'company_name' => $this->faker->randomElement([$this->faker->company(), null, null]),
            'depository' => $this->faker->randomElement([$this->faker->sentence, null, null]),
            'delivery_address_id' => $delivery,
            'billing_address_id' => $billing,
        ];
    }
}
