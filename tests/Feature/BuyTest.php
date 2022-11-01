<?php

namespace Tests\Feature;

use App\Jobs\CreateOrdersForBuy;
use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\DeliveryService;
use App\Notifications\SendInvoice;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BuyTest extends TestCase
{
    public function test_export()
    {
        $buy = Buy::factory()->create();

        //$response = $this->get(route('buy.export.bill', $buy));
        //$response->assertRedirect();

        Sanctum::actingAs($this->admin);
        $response = $this->get(route('buy.export.bill', $buy));
        $response->assertDownload();
    }

    public function test_createOrdersForBuyJob()
    {
        $customer = Customer::factory([
            'delivery_address_id' => null,
            'delivery_service_id' => DeliveryService::where('pickup', '=', 1)->inRandomOrder()->first()->id,
        ])->create();
        $buy = Buy::factory([
            'customer_id' => $customer->id,
            'paid' => 1,
        ])->create();

        self::assertEquals(0, $customer->orders->count());

        CreateOrdersForBuy::dispatchSync($buy);
        $customer->refresh();

        self::assertGreaterThan(0, $customer->orders->count());
    }

    public function test_update()
    {
        $buy = Buy::factory([
            'paid' => false,
        ])->create();
        Queue::fake();

        $response = $this->json('patch', '/api/buy/'.$buy->id, [
            'paid' => true,
        ]);
        $response->assertStatus(401); // unauthorized

        Sanctum::actingAs($this->admin);
        $response = $this->json('patch', '/api/buy/'.$buy->id, [
            'paid' => true,
        ]);
        $response->assertOk();
        Queue::assertPushed(CreateOrdersForBuy::class);
        $buy->refresh();
        self::assertEquals(1, $buy->paid);

        $response = $this->json('patch', '/api/buy/'.$buy->id, [
            'paid' => false,
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

    public function test_issue() {
        $customer = Customer::factory()->create();
        $bundle = Bundle::where('trial', 0)->first();
        $previous_buy = Buy::factory()->create([
            'customer_id' => $customer->id,
            'bundle_id' => $bundle->id,
        ]);
        $customer->refresh();

        self::assertNotNull($customer->user);
        self::assertEquals(1, $customer->buys->count());

        Notification::fake();

        Sanctum::actingAs($this->admin);
        $response = $this->json('post', '/api/buy', [
            'customer_id' => $customer->id,
            'product_id' => $bundle->product->id,
        ]);
        $customer->refresh();
        $response->assertOk();
        self::assertEquals(2, $customer->buys->count());

        Notification::assertSentTo($customer->user, SendInvoice::class);
    }

    public function test_delete() {
        $buy = Buy::factory()->create();

        Sanctum::actingAs($this->customer);
        $response = $this->json('delete', '/api/buy/'.$buy->id);
        $response->assertStatus(403);

        Sanctum::actingAs($this->admin);
        $response = $this->json('delete', '/api/buy/'.$buy->id);
        $response->assertOk();
    }

    public function test_customer() {
        $customer = Customer::factory()->create();
        $buy = Buy::factory([
            'customer_id' => $customer->id,
        ])->count(3)->create();

        Sanctum::actingAs($this->admin);
        $response = $this->json('get', '/api/buys/'.$customer->id);
        $response->assertOk();
        $response->assertJson(['customer' => [], 'buys' => []]);
    }
}
