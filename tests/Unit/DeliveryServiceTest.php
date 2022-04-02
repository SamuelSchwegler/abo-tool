<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\Customer;
use App\Models\DeliveryService;
use App\Models\Postcode;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DeliveryServiceTest extends TestCase
{
    use WithFaker;

    public function test_customers() {
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
        Log::info($customers);
        self::assertEquals($customers->first()->id, $customer->id);
    }
}
