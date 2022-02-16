<?php

namespace Database\Seeders;

use App\Models\Bundle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        Bundle::create([
            'name' => '12er Abo Gross a 44 CHF',
            'deliveries' => 12,
            'price' => 52800,
        ]);
    }
}
