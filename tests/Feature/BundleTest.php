<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class BundleTest extends TestCase
{
    use WithFaker;

    public function test_submitBuy()
    {
        $bundle = Bundle::inRandomOrder()->first();
        $email = $this->faker->safeEmail();
        $user_count = User::count();
        $customer_count = Customer::count();

        $response = $this->post('/api/bundle/' . $bundle->id . '/buy', []);
        $response->assertSessionHasErrors(['email', 'password']);
        self::assertEquals($user_count, User::count());
        self::assertEquals($customer_count, Customer::count());
        self::assertFalse(Auth::check());

        $response = $this->post('/api/bundle/' . $bundle->id . '/buy', [
            'email' => $email,
            'password' => Str::random(8)
        ]);
        self::assertTrue(Auth::check());
        $response->assertSessionHasErrors(['first_name', 'last_name']);
        self::assertEquals($user_count + 1, User::count());
        self::assertEquals($customer_count, Customer::count());

        $data = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'delivery_option' => 'match',
            'delivery_address' => [
                'street' => $this->faker->streetAddress(),
                'postcode' => $this->faker->postcode(),
                'city' => $this->faker->city()
            ]
        ];
        $response = $this->post('/api/bundle/' . $bundle->id . '/buy', $data);

        self::assertEquals($customer_count + 1, Customer::count());
        self::assertTrue(Auth::check());
        $response->assertSessionDoesntHaveErrors();
    }
}
