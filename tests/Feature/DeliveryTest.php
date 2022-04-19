<?php

namespace Tests\Feature;

use App\Jobs\CreateDeliveries;
use App\Jobs\DeliveryCreateOrders;
use App\Jobs\DeliveryRemoveOrders;
use App\Models\Address;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Postcode;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    use WithFaker;

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
           'approved' => false,
            'deadline' => now()->addDays(3),
            'date' => now()->addDays(5)
        ]);
        Queue::fake();
        Sanctum::actingAs($this->admin);

        $response = $this->json('patch', '/api/delivery/'.$delivery->id.'/toggle-approved');
        $response->assertOk();
        $delivery->refresh();
        self::assertEquals(1, $delivery->approved);
        Queue::assertPushed(DeliveryCreateOrders::class);

        $response = $this->json('patch', '/api/delivery/'.$delivery->id.'/toggle-approved');
        $response->assertOk();
        $delivery->refresh();
        self::assertEquals(0, $delivery->approved);
        Queue::assertPushed(DeliveryRemoveOrders::class);
    }

    public function test_createOrdersForDeliveryJob() {
        // Test Needed Relations
        $service = DeliveryService::factory()->create();
        $postcode = $this->faker->postcode();
        Postcode::create([
            'postcode' => $postcode,
            'delivery_service_id' => $service->id
        ]);

        $delivery_address = Address::factory()->create([
            'postcode' => $postcode
        ]);
        $customer = Customer::factory()->create([
            'delivery_address_id' => $delivery_address->id
        ]);

        $service->refresh();
        $customers = $service->customers();
        self::assertEquals($customers->first()->id, $customer->id);

        // Test Job
        self::assertEquals(0, $customer->orders->count());
        $buy = Buy::factory()->create([
            'customer_id' => $customer->id
        ]);
        $delivery = Delivery::factory()->create([
            'delivery_service_id' => $service->id,
            'approved' => false,
            'deadline' => now()->addDays(3),
            'date' => now()->addDays(5)
        ]);

        DeliveryCreateOrders::dispatch($delivery);
        $delivery->refresh();
        $customer->refresh();

        self::assertEquals(1, $customer->orders->count());
        self::assertEquals(1, $delivery->orders->count());

        DeliveryRemoveOrders::dispatch($delivery);
        $delivery->refresh();
        $customer->refresh();

        self::assertEquals(0, $customer->orders->count());
        self::assertEquals(0, $delivery->orders->count());
    }

    public function test_exportDeliveryNotes()
    {
        $this->actingAs($this->admin);
        $delivery = Delivery::has('orders')->inRandomOrder()->first();

        $response = $this->get(route('delivery-notes.export', $delivery));
        $response->assertOk();
        //$response->assertDownload();
    }
}
