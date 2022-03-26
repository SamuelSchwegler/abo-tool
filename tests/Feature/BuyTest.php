<?php

namespace Tests\Feature;

use App\Models\Buy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BuyTest extends TestCase
{
    public function test_export() {
        $buy = Buy::factory()->create();

        $response = $this->get(route('buy.export.bill', $buy));
        $response->assertRedirect();

        $this->actingAs($this->admin);
        $response = $this->get(route('buy.export.bill', $buy));
        $response->assertDownload();
    }
}
