<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\DeliveryService;
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
        $customer = Customer::factory()->create();

        self::assertEquals(0, sizeof($customer->productBalances()));

        $buy = Buy::factory()->create([
            'customer_id' => $customer->id,
            'paid' => 0,
        ]);

        self::assertEquals(0, sizeof($customer->productBalances()));

        $buy->update([
            'paid' => 1,
        ]);

        self::assertEquals(1, sizeof($customer->productBalances()));
    }
}
