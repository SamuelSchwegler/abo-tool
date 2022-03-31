<?php

namespace Tests\Feature;

use App\Jobs\CreateOrdersForBuy;
use App\Models\Buy;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BuyTest extends TestCase
{
    public function test_export()
    {
        $buy = Buy::factory()->create();

        $response = $this->get(route('buy.export.bill', $buy));
        $response->assertRedirect();

        $this->actingAs($this->admin);
        $response = $this->get(route('buy.export.bill', $buy));
        $response->assertDownload();
    }

    public function test_createOrdersForBuyJob()
    {
        $customer = Customer::factory()->create();
        $buy = Buy::factory([
            'customer_id' => $customer->id
        ])->create();

        self::assertEquals(0, $customer->orders->count());

        CreateOrdersForBuy::dispatchSync($buy);
        $customer->refresh();

        self::assertGreaterThan(0, $customer->orders->count());
    }

    public function test_update()
    {
        $buy = Buy::factory([
            'paid' => false
        ])->create();
        Queue::fake();

        $response = $this->json('patch', '/api/buy/' . $buy->id, [
            'paid' => true
        ]);
        $response->assertStatus(401); // unauthorized

        Sanctum::actingAs($this->admin);
        $response = $this->json('patch', '/api/buy/' . $buy->id, [
            'paid' => true
        ]);
        $response->assertOk();
        Queue::assertPushed(CreateOrdersForBuy::class);
        $buy->refresh();
        self::assertEquals(1, $buy->paid);

        $response = $this->json('patch', '/api/buy/' . $buy->id, [
            'paid' => false
        ]);
        $response->assertOk();
        $buy->refresh();
        self::assertEquals(0, $buy->paid);
    }

    public function test_payments() {
        Sanctum::actingAs($this->admin);
        $response = $this->json('get', '/api/payments/');
        $response->assertOk();
    }

    public function test_buys() {
        $buy = Buy::factory()->create();

        Sanctum::actingAs($this->admin);
        $response = $this->json('get', '/api/buy/'.$buy->id);
        $response->assertOk();
    }
}
