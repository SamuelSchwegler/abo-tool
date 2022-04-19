<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Bundle;
use App\Models\Customer;
use App\Models\Postcode;
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
        $address_count = Address::count();

        $response = $this->json('post', '/api/bundle/' . $bundle->id . '/buy', []);
        $response->assertStatus(422);
        self::assertEquals($user_count, User::count());
        self::assertEquals($customer_count, Customer::count());
        self::assertFalse(Auth::check());

        $response = $this->json('post', '/api/bundle/' . $bundle->id . '/buy', [
            'email' => $email,
            'password' => Str::random(8)
        ]);
        self::assertTrue(Auth::check());
        $response->assertStatus(422);
        self::assertEquals($user_count + 1, User::count());
        self::assertEquals($customer_count, Customer::count());

        $data = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'delivery_option' => 'match',
            'delivery_address' => [
                'street' => $this->faker->streetAddress(),
                'postcode' => '111',
                'city' => $this->faker->city()
            ]
        ];

        $response = $this->json('post', '/api/bundle/' . $bundle->id . '/buy', $data);
        $response->assertStatus(422);

        $data['delivery_address']['postcode'] = Postcode::inRandomOrder()->first()->postcode;
        $response = $this->json('post', '/api/bundle/' . $bundle->id . '/buy', $data);
        $response->assertOk();
        self::assertEquals($customer_count + 1, Customer::count());
        self::assertEquals($address_count + 1, Address::count());
        self::assertTrue(Auth::check());

        // Variante mit Split
        $data = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'delivery_option' => 'split',
            'delivery_address' => [
                'street' => $this->faker->streetAddress(),
                'postcode' => Postcode::inRandomOrder()->first()->postcode,
                'city' => $this->faker->city()
            ]
        ];
        $response = $this->post('/api/bundle/' . $bundle->id . '/buy', $data);
        $response->assertSessionHasErrors(['billing_address']);

        $data = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'delivery_option' => 'split',
            'delivery_address' => [
                'street' => $this->faker->streetAddress(),
                'postcode' => Postcode::inRandomOrder()->first()->postcode,
                'city' => $this->faker->city()
            ],
            'billing_address' => [
                'street' => $this->faker->streetAddress(),
                'postcode' => $this->faker->postcode(),
                'city' => $this->faker->city()
            ]
        ];
        $response = $this->post('/api/bundle/' . $bundle->id . '/buy', $data);
        $response->assertOk();

        self::assertEquals($customer_count + 1, Customer::count());
        self::assertEquals($address_count + 2, Address::count());

        $data = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'delivery_option' => 'pickup',
            'billing_address' => [
                'street' => $this->faker->streetAddress(),
                'postcode' => $this->faker->postcode(),
                'city' => $this->faker->city()
            ]
        ];
        $response = $this->post('/api/bundle/' . $bundle->id . '/buy', $data);
        self::assertEquals($customer_count + 1, Customer::count());
        self::assertEquals($address_count + 1, Address::count());
        $response->assertOk();
    }
}
