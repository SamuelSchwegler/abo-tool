<?php

namespace Tests\Feature;

use App\Jobs\CreateDeliveries;
use App\Jobs\CreateOrdersForDelivery;
use App\Models\Delivery;
use App\Models\DeliveryService;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    public function test_deliveries() {
        Sanctum::actingAs($this->admin);

        $response = $this->json('get','/api/deliveries');
        $response->assertOk();

        $response = $this->json('get','/api/deliveries?start='.now()->format('Y-m-d').'&order_by=deadline');
        $response->assertOk();

        $response = $this->json('get','/api/deliveries?start='.now()->format('Y-m-d').'&delivery_service_ids[]='.DeliveryService::inRandomOrder()->first()->id);
        $response->assertOk();
    }

    public function test_delivery() {
        $delivery = Delivery::factory()->create();

        Sanctum::actingAs($this->admin);

        $response = $this->json('get','/api/delivery/'.$delivery->id);
        $response->assertOk();
    }

    public function test_createDeliveriesJob() {
        $service = DeliveryService::factory([
            'days' => ['sun']
        ])->create();

        self::assertEquals(0, $service->deliveries->count());
        CreateDeliveries::dispatchSync($service);
        $service->refresh();
        $after_count = $service->deliveries->count();
        self::assertGreaterThan(0, $after_count);

        CreateDeliveries::dispatchSync();
        $service->refresh();
        self::assertEquals($after_count, $service->deliveries->count());
    }

    public function test_toggleApproved() {
        $delivery = Delivery::factory()->create([
           'approved' => false
        ]);
        Queue::fake();
        Sanctum::actingAs($this->admin);

        $response = $this->json('patch', '/api/delivery/'.$delivery->id.'/toggle-approved');
        $response->assertOk();
        $delivery->refresh();
        self::assertEquals(1, $delivery->approved);
        Queue::assertPushed(CreateOrdersForDelivery::class);
    }
}
