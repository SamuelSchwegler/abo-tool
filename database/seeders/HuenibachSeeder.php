<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Bundle;
use App\Models\DeliveryService;
use App\Models\Product;
use App\Models\Setting;
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
            'name' => '12er Abo Gross a 44 CHF',
            'deliveries' => 12,
            'price' => 52800,
            'product_id' => $klein->id,
        ]);

        Bundle::create([
            'name' => '6er Probe Abo Gross a 44 CHF',
            'deliveries' => 12,
            'price' => 26400,
            'product_id' => $klein->id,
        ]);

        Bundle::create([
            'name' => '12er Abo Klein à 29 CHF',
            'deliveries' => 12,
            'price' => 34800,
            'product_id' => $gross->id,
        ]);

        Bundle::create([
            'name' => '6er Probe Abo klein à 29 CHF',
            'deliveries' => 12,
            'price' => 17400,
            'product_id' => $gross->id,
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
    }
}
