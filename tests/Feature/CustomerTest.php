<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\Postcode;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use WithFaker;

    public function test_customers()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->json('get', '/api/customers/');
        $response->assertOk();
    }

    public function test_customer()
    {
        Sanctum::actingAs($this->admin);
        $customer = Customer::inRandomOrder()->first();

        $response = $this->json('get', '/api/customer/'.$customer->id);
        $response->assertOk();
    }

    public function test_update()
    {
        Sanctum::actingAs($this->admin);
        $customer = Customer::factory()->create();

        $data = [
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'phone' => $customer->phone,
            'delivery_option' => 'match',
            'delivery_address' => [
                'street' => $this->faker->streetAddress(),
                'postcode' => Postcode::inRandomOrder()->first()->postcode,
                'city' => $this->faker->city(),
            ],
        ];

        $response = $this->json('patch', '/api/customer/'.$customer->id, $data);

        $response->assertOk();
        $customer->refresh();
        self::assertEquals($data['delivery_address']['city'], $customer->delivery_address->city);

        $data['delivery_option'] = 'split';
        $data['billing_address'] = [
            'street' => $this->faker->streetAddress(),
            'postcode' => $this->faker->postcode(),
            'city' => $this->faker->city(),
        ];

        $response = $this->json('patch', '/api/customer/'.$customer->id, $data);

        $response->assertOk();
        $customer->refresh();
        self::assertEquals($data['billing_address']['city'], $customer->billing_address->city);

        $data['delivery_option'] = 'pickup';
        $data['delivery_address'] = null;

        $response = $this->json('patch', '/api/customer/'.$customer->id, $data);
        $response->assertOk();
    }

    public function test_store()
    {
        $customer_count = Customer::count();
        $user_count = User::count();
        Sanctum::actingAs($this->admin);

        $data = [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
        ];

        $response = $this->json('post', '/api/customer/', $data);

        $response->assertOk();
        self::assertEquals($customer_count + 1, Customer::count());
        self::assertEquals($user_count + 1, User::count());
    }

    public function test_usedOrders()
    {
        $bundle = Bundle::inRandomOrder()->first();

        $customer = Customer::factory()->create([
            'used_orders' => [
                $bundle->product->id => 12,
            ],
        ]);

        $buy = Buy::factory()->create([
            'customer_id' => $customer->id,
            'bundle_id' => $bundle->id,
            'paid' => true,
        ]);
        $customer->refresh();
        self::assertEquals(1, $customer->buys->count());
        self::assertEquals(1, $customer->productBuys()->count());

        $product_balances = $customer->productBalances();
        self::assertIsArray($product_balances);
        self::assertEquals($bundle->deliveries - 12, $product_balances[$bundle->product->id]->balance);

        // update
        Sanctum::actingAs($this->admin);
        $response = $this->patch('/api/customer/'.$customer->id.'/used-orders', [
            'product_id' => $bundle->product_id,
            'value' => 6,
        ]);
        $response->assertOk();

        $customer->refresh();
        $product_balances = $customer->productBalances();
        self::assertIsArray($product_balances);
        self::assertEquals($bundle->deliveries - 6, $product_balances[$bundle->product->id]->balance);
    }
}
