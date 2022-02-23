<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\User;
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
        \App\Models\User::factory(10)->create()->each(function ($user) {
            $user->assignRole('customer');
        });
        $admin = User::factory()->create([
            'email' => 'samuel@webtheke.ch',
            'password' => bcrypt('1234')
        ]);

        $admin->assignRole('admin');

        Customer::factory(10)->create();

        $this->call(DeliverySeeder::class);
        $this->call(HuenibachSeeder::class);
    }
}
