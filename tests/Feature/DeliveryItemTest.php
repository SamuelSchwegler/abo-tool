<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\DeliveryProductItem;
use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeliveryItemTest extends TestCase
{
    public function testDeliveriesItem()
    {
        $date = now();
        $product = Product::create([
            'name' => 'Testproduct',
        ]);
        $delivery1 = Delivery::factory()->create([
            'date' => $date,
        ]);
        $order1 = Order::factory()->create([
            'product_id' => $product->id,
            'delivery_id' => $delivery1->id,
        ]);

        $delivery2 = Delivery::factory()->create([
            'date' => $date,
        ]);
        $order2 = Order::factory()->create([
            'product_id' => $product->id,
            'delivery_id' => $delivery1->id,
        ]);

        $query = DeliveryProductItem::whereDate('date', $date->format('Y-m-d'))->where('product_id', $product->id);
        self::assertEquals(0, (clone $query)->count());

        $items_count = Item::count();
        $item_name = Str::random();

        Sanctum::actingAs($this->admin);
        $response = $this->json('post', '/api/deliveries/'.$date->format('Y-m-d').'/'.$product->id.'/item', [
            'item' => $item_name,
        ]);
        $response->assertOk();

        self::assertEquals($items_count + 1, Item::count());
        self::assertEquals(1, (clone $query)->count());

        $response = $this->json('post', '/api/deliveries/'.$date->format('Y-m-d').'/'.$product->id.'/item', [
            'item' => Str::random(),
        ]);
        $response->assertOk();

        self::assertEquals($items_count + 2, Item::count());
        self::assertEquals(2, (clone $query)->count());

        $item = Item::where('name', $item_name)->first();
        self::assertNotNull($item);

        // bearbeiten von einzelner Lieferung
        self::assertEquals(0, $delivery1->items->count());
        $response = $this->json('post', '/api/delivery/'.$delivery1->id.'/'.$product->id.'/item', [
            'item' => Str::random(),
        ]);
        $response->assertOk();
        $delivery1->refresh();
        self::assertEquals(3, $delivery1->items->count());

        // entfernen von Item von einzelner Lieferung
        $response = $this->json('delete', '/api/delivery/'.$delivery2->id.'/'.$product->id.'/item/'.$item->id, [
            'item' => $item_name,
        ]);
        $response->assertOk();
        $delivery2->refresh();
        self::assertEquals(1, $delivery2->items->count());

        $response = $this->json('delete', '/api/deliveries/'.$date->format('Y-m-d').'/'.$product->id.'/item/'.$item->id, [
            'item' => $item_name,
        ]);
        $response->assertOk();
        self::assertEquals(1, (clone $query)->count());

    }
}
