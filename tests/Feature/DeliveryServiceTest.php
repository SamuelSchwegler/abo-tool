<?php

namespace Tests\Feature;

use App\Models\DeliveryService;
use App\Models\Postcode;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeliveryServiceTest extends TestCase
{
    use WithFaker;

    public function test_store()
    {
        $this->actingAs($this->admin);

        $count = DeliveryService::count();

        $response = $this->post('/api/delivery-service', [
            'name' => Str::random(),
            'days' => ['sat'],
            'deadline_distance' => 1
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertOk();
        self::assertEquals($count + 1, DeliveryService::count());
    }

    public function test_addRemovePostcode() {
        $this->actingAs($this->admin);

        $postcode_count = Postcode::count();
        $service1 = DeliveryService::factory()->create([
            'name' => 'Postcode Adder'
        ]);
        $service2 = DeliveryService::factory()->create([
            'name' => 'Postcode Adder 2'
        ]);
        $postcode = $this->faker->numberBetween(1000, 9999);

        $response = $this->post('/api/delivery-service/'.$service1->id.'/add/', [
            'postcode' => $postcode,
        ]);
        $response->assertSessionDoesntHaveErrors();
        $response->assertOk();

        $service1->refresh();
        $service2->refresh();
        self::assertEquals($postcode_count + 1, Postcode::count());
        self::assertEquals(1, $service1->postcodes->count());
        self::assertEquals(0, $service2->postcodes->count());

        // Test Postcode
        $response = $this->json('get', '/api/postcode-delivery-service', [
            'postcode' => $postcode
        ]);
        $response->assertOk();
        self::assertEquals($service1->id, $response->json(['service'])['id']);

        $response = $this->post('/api/delivery-service/'.$service2->id.'/add/', [
            'postcode' => $postcode,
        ]);
        $response->assertSessionDoesntHaveErrors();
        $response->assertOk();

        $service1->refresh();
        $service2->refresh();
        self::assertEquals($postcode_count + 1, Postcode::count());
        self::assertEquals(0, $service1->postcodes->count());
        self::assertEquals(1, $service2->postcodes->count());

        // Test Postcode
        $response = $this->json('get', '/api/postcode-delivery-service', [
            'postcode' => $postcode
        ]);
        $response->assertOk();
        self::assertEquals($service2->id, $response->json(['service'])['id']);

        $response = $this->post('/api/delivery-service/'.$service2->id.'/remove/', [
            'postcode' => $postcode,
        ]);
        $response->assertSessionDoesntHaveErrors();
        $response->assertOk();

        $service1->refresh();
        $service2->refresh();
        self::assertEquals($postcode_count, Postcode::count());
        self::assertEquals(0, $service1->postcodes->count());
        self::assertEquals(0, $service2->postcodes->count());
    }

    public function test_Update() {
        $this->actingAs($this->admin);

        $service = DeliveryService::factory()->create([
            'name' => 'alt',
        ]);

        $response = $this->json('patch', '/api/delivery-service/'.$service->id, [
            'name' => 'neu',
            'days' => ['sat'],
            'deadline_distance' => 1
        ]);
        $service->refresh();
        $response->assertOk();
        self::assertEquals('neu', $service->name);
    }
}
