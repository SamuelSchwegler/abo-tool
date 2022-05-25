<?php

namespace Tests\Feature;

use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\Postcode;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

        $response = $this->json('patch', '/api/customer/'.$customer->id, [
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'phone' => $customer->phone,
            'delivery_option' => 'match',
            'delivery_address' => [
                'street' => $this->faker->streetAddress(),
                'postcode' => Postcode::inRandomOrder()->first()->postcode,
                'city' => $this->faker->city(),
            ],
        ]);

        $response->assertOk();
    }

    public function test_usedOrders() {
        $this->markTestIncomplete('in entwicklung');
        $bundle = Bundle::inRandomOrder()->first();
        $customer = Customer::factory()->create([
            'used_orders' => [
                $bundle->product->id => 12
            ]
        ]);
        $buy = Buy::factory()->create([
            'customer_id' => $customer->id,
            'bundle_id' => $bundle->id
        ]);
        $buy->refresh();

        var_dump($customer->productBalances());
        //self::assertEquals(12, $customer->productBalances()[$bundle->product->id]->balance);
    }
}
