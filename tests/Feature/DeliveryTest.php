<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\DeliveryService;
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
}
