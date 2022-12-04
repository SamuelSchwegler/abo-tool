<?php

namespace Tests\Unit;

use App\Jobs\CreateDeliveries;
use App\Jobs\CustomerChangeDelivery;
use App\Models\Address;
use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Order;
use App\Models\Postcode;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function test_deliveryService()
    {
        $postcode = random_int(1000, 9999);

        $delivery = Address::factory()->create([
            'postcode' => $postcode,
        ]);
        $customer = Customer::factory()->create([
            'delivery_address_id' => $delivery->id,
        ]);

        $service = DeliveryService::factory()->create(['name' => Str::random(3)]);
        Postcode::create([
            'delivery_service_id' => $service->id,
            'postcode' => $postcode,
        ]);

        self::assertEquals($service->id, $customer->delivery_service()->id);
    }

    public function test_productBalances()
    {
        $bundle = Bundle::inRandomOrder()->first();
        $product = $bundle->product;
        $customer = Customer::factory()->create();

        self::assertEquals(0, sizeof($customer->productBalances()));

        $buy = Buy::factory()->create([
            'customer_id' => $customer->id,
            'bundle_id' => $bundle->id,
            'paid' => 0,
        ]);

        self::assertEquals(0, sizeof($customer->productBalances()));

        $buy->update([
            'paid' => 1,
        ]);

        self::assertEquals(1, sizeof($customer->productBalances()));

        $productBuys = $customer->productBuys($product);
        self::assertEquals(1, $productBuys->count());
        self::assertEquals($product->id, $productBuys->first()->product_id);

        self::assertEquals($bundle->deliveries, $customer->creditOfProduct($product));
        self::assertEquals($bundle->deliveries, $customer->creditOfProduct($product, true));

        // Mit bestellungen
        $deliveryPast = Delivery::factory()->create([
            'date' => now()->subWeeks(2),
            'deadline' => now()->subWeeks(2),
            'approved' => true,
        ]);

        Order::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'delivery_id' => $deliveryPast->id,
            'canceled' => 0,
        ]);

        $customer->refresh();
        self::assertEquals($bundle->deliveries - 1, $customer->creditOfProduct($product));
        self::assertEquals($bundle->deliveries - 1, $customer->creditOfProduct($product, true));

        $deliverySoon = Delivery::factory()->create([
            'date' => now()->addDays(2),
            'deadline' => now()->addDays(2),
            'approved' => true,
        ]);

        Order::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'delivery_id' => $deliverySoon->id,
            'canceled' => 0,
        ]);

        $customer->refresh();

        self::assertEquals($bundle->deliveries - 1, $customer->creditOfProduct($product));
        self::assertEquals($bundle->deliveries - 2, $customer->creditOfProduct($product, true));
    }

    public function test_customerChangeDelivery()
    {
        $pickupService = DeliveryService::where('pickup', 1)->first();
        $deliveryService = DeliveryService::factory()->create([
            'pickup' => 0,
            'days' => $pickupService->days,
            'deadline_distance' => $pickupService->deadline_distance,
        ]);

        $customer = Customer::factory()->create([
            'delivery_address_id' => null,
            'delivery_service_id' => $pickupService->id,
        ]);

        self::assertTrue($pickupService->deliveries->count() > 0);
        self::assertFalse($deliveryService->deliveries->count() > 0);

        CreateDeliveries::dispatchSync($deliveryService);
        $deliveryService->refresh();
        self::assertTrue($deliveryService->deliveries->count() > 0);
        $deliveryService->deliveries()->update(['approved' => true]);
        $pickupService->deliveries()->update(['approved' => true]);

        foreach ($deliveryService->deliveries as $delivery) {
            Order::factory()->create([
                'delivery_id' => $delivery->id,
                'customer_id' => $customer->id,
            ]);
        }

        CustomerChangeDelivery::dispatch($customer);

        $next = $customer->orders()->whereHas('delivery', function ($query) {
            $query->where('deadline', '>=', now()->startOfDay()->format('Y-m-d H:i:s'));
        })->get();
        self::assertTrue($next->count() > 0);
        self::assertEquals($pickupService->id, $next->first()->delivery->delivery_service->id);
    }
}
