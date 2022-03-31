<?php

namespace Tests\Feature;

use App\Models\Delivery;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    public function test_customers() {
        Sanctum::actingAs($this->admin);

        $response = $this->json('get','/api/customers/');
        $response->assertOk();
    }
}
