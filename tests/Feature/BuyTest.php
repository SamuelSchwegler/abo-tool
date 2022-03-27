<?php

namespace Tests\Feature;

use App\Jobs\CreateOrdersForBuy;
use App\Models\Buy;
use App\Models\Customer;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BuyTest extends TestCase
{
    public function test_export() {
        $buy = Buy::factory()->create();

        $response = $this->get(route('buy.export.bill', $buy));
        $response->assertRedirect();

        Sanctum::actingAs($this->admin);
        $response = $this->get(route('buy.export.bill', $buy));
        $response->assertDownload();
    }

    public function test_createOrdersForBuyJob() {
        $customer = Customer::factory()->create();
        $buy = Buy::factory([
            'customer_id' => $customer->id
        ])->create();

        self::assertEquals(0, $customer->orders->count());

        CreateOrdersForBuy::dispatchSync($buy);
        $customer->refresh();

        self::assertGreaterThan(0,$customer->orders->count());

    }
}
