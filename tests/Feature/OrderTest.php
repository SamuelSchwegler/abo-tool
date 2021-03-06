<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_orders() {
        $this->actingAs($this->admin);
        $response = $this->get('/api/orders');
        $response->assertOk();
    }

    public function test_update() {
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

    public function test_toggleCancel() {
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
}
