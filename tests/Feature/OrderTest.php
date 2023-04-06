<?php

namespace Tests\Feature;

use App\Jobs\CreateReadyOrders;
use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Order;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_orders()
    {
        $this->actingAs($this->admin);
        $response = $this->get('/api/orders');
        $response->assertOk();
    }

    public function test_update()
    {
        $this->actingAs($this->admin);
        $deadline = today();

        $delivery = Delivery::factory()->create([
            'deadline' => $deadline,
        ]);
        $order = Order::factory()->create([
            'delivery_id' => $delivery->id,
            'depository' => '',
        ]);
        self::assertFalse($order->deadlinePassed());

        $response = $this->patch('/api/order/'.$order->id, [
            'depository' => 'Komm wir gehen',
        ]);
        $response->assertOk();
        $order->refresh();

        self::assertEquals('Komm wir gehen', $order->depository);

        $delivery->update(['deadline' => $deadline->subMinute()]);
        $order->refresh();

        $text = Str::random();
        $response = $this->patch('/api/order/'.$order->id, [
            'depository' => $text,
        ]);
        self::assertTrue($order->deadlinePassed());
        $response->assertOk(); // exception
        $order->refresh();
        self::assertEquals($text, $order->depository);

        $this->actingAs($this->customer);
        $response = $this->patch('/api/order/'.$order->id, [
            'depository' => 'Komm wir 333',
        ]);
        $response->assertStatus(500); // exception
        $order->refresh();
        self::assertEquals($text, $order->depository);
    }

    public function test_toggleCancel()
    {
        $this->actingAs($this->admin);

        $delivery = Delivery::factory()->create([
            'deadline' => now()->addDay(),
        ]);
        $order = Order::factory()->create([
            'delivery_id' => $delivery->id,
            'depository' => '',
            'canceled' => true,
        ]);

        $response = $this->patch('/api/order/'.$order->id.'/toggle-cancel');
        $response->assertOk();

        $order->refresh();
        self::assertEquals(0, $order->canceled);

        $response = $this->patch('/api/order/'.$order->id.'/toggle-cancel');
        $response->assertOk();

        $order->refresh();
        self::assertEquals(1, $order->canceled);
    }

    public function test_createReadyOrders()
    {
        $bundle = Bundle::inRandomOrder()->first();
        $service = DeliveryService::factory([
            'pickup' => true,
        ])->create();

        $pastDelivery = Delivery::factory()->create([
            'delivery_service_id' => $service->id,
            'date' => now()->subDays(4),
            'deadline' => now()->subDays(7),
            'approved' => true,
        ]);

        $delivery = Delivery::factory()->create([
            'delivery_service_id' => $service->id,
            'date' => now()->addDays(3),
            'deadline' => now()->addDay(),
            'approved' => true,
        ]);

        $customer = Customer::factory([
            'delivery_address_id' => null,
            'delivery_service_id' => $service->id,
        ])->create();

        $buy = Buy::factory([
            'customer_id' => $customer->id,
            'paid' => 1,
            'bundle_id' => $bundle,
        ])->create();

        $customer->refresh();

        self::assertEquals($service->id, $customer->delivery_service()->id);
        self::assertTrue($service->customers()->contains($customer->id));
        self::assertEquals(0, $customer->orders->count());
        self::assertEquals($bundle->deliveries, $customer->creditOfProduct($bundle->product));

        CreateReadyOrders::dispatchSync();

        $customer->refresh();

        self::assertEquals(1, $customer->orders->count());
        self::assertEquals($delivery->id, $customer->orders->first()->delivery_id);
    }
}
