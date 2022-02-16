<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Address::factory(20)->create();
        \App\Models\User::factory(10)->create();
        Customer::factory(10)->create();

        $this->call(HuenibachSeeder::class);
    }
}
