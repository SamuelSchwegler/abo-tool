<?php

namespace Tests\Feature;

use App\Models\DeliveryService;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliveryServiceTest extends TestCase
{
    public function test_store()
    {
        $this->actingAs($this->admin);

        $count = DeliveryService::count();

        $response = $this->post('/api/delivery-service', [
            'name' => Str::random(),
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertOk();
        self::assertEquals($count + 1, DeliveryService::count());
    }
}
