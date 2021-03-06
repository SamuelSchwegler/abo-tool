<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Bundle;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;

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
            'short_description' => 'Ideal für 3 - 4 Personen',
            'deliveries' => 12,
            'price' => 528,
            'product_id' => $gross->id,
        ]);

        Bundle::create([
            'name' => 'Gemüseabo klein',
            'short_description' => 'Ideal für 1 - 2 Personen',
            'deliveries' => 12,
            'price' => 348,
            'product_id' => $klein->id,
        ]);

        Bundle::create([
            'name' => 'Probeabo gross',
            'deliveries' => 6,
            'price' => 264,
            'product_id' => $gross->id,
            'trial' => true,
        ]);

        Bundle::create([
            'name' => 'Probeabo klein',
            'deliveries' => 6,
            'price' => 174,
            'product_id' => $klein->id,
            'trial' => true,
        ]);

        $address = Address::create([
            'street' => 'Chartreusestrasse 7',
            'postcode' => '3626',
            'city' => 'Hünibach',
        ]);

        Setting::create([
            'name' => 'Gartenbauschule Huenibach',
            'address_id' => $address->id,
            'besr_id' => '',
            'iban' => 'CH5330790016597781328',
        ]);
    }
}
