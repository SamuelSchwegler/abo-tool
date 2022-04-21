<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Bundle;
use App\Models\DeliveryService;
use App\Models\Item;
use App\Models\ItemOrigin;
use App\Models\Product;
use App\Models\Setting;
use Database\Factories\ItemFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HuenibachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $klein = Product::create([
            'name' => 'kleine Gemüsetasche',
        ]);

        $gross = Product::create([
            'name' => 'grosse Gemüsetasche',
        ]);

        Bundle::create([
            'name' => 'Gemüseabo gross',
            'deliveries' => 12,
            'price' => 528,
            'product_id' => $gross->id,
        ]);

        Bundle::create([
            'name' => 'Gemüseabo klein',
            'deliveries' => 12,
            'price' => 348,
            'product_id' => $klein->id,
        ]);

        Bundle::create([
            'name' => 'Probeabo gross',
            'deliveries' => 6,
            'price' => 264,
            'product_id' => $gross->id,
            'trial' => true
        ]);

        Bundle::create([
            'name' => 'Probeabo klein',
            'deliveries' => 6,
            'price' => 174,
            'product_id' => $klein->id,
            'trial' => true
        ]);

        $address = Address::create([
            'street' => 'Chartreusestrasse 7',
            'postcode' => '3626',
            'city' => 'Hünibach'
        ]);

        Setting::create([
            'name' => 'Gartenbauschule Huenibach',
            'address_id' => $address->id,
            'besr_id' => '',
            'iban' => 'CH5330790016597781328'
        ]);

        ItemOrigin::factory()->count(3)->create();
        Item::factory()->count(10)->create();
    }
}
