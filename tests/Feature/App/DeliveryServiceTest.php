<?php

namespace Tests\Feature\App;

use App\Models\DeliveryService;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;
use function route;

class DeliveryServiceTest extends TestCase
{
    public function test_create()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('delivery-service.create'));
        $response->assertOk();
        $response->assertViewIs('delivery-service.create');
    }

    public function test_edit()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('delivery-service.edit-default'));
        $response->assertOk();
        $response->assertViewIs('delivery-service.edit');
    }

    public function test_store()
    {
        $this->actingAs($this->admin);

        $count = DeliveryService::count();

        $response = $this->post(route('delivery-service.create'), [
            'name' => Str::random()
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect();
        self::assertEquals($count + 1, DeliveryService::count());
    }
}
