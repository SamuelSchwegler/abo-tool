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
            'email' => 'demo@webtheke.ch',
            'password' => bcrypt('1234')
        ]);
        $admin->assignRole('admin');

        $customer = User::factory()->create([
            'email' => 'demo-user@webtheke.ch',
            'password' => bcrypt('Demo!User19*Test')
        ]);
        $customer->assignRole('customer');

        Customer::factory(10)->create();
        Customer::factory()->create([
           'user_id' => $customer->id
        ]);

        $this->call(HuenibachSeeder::class);
        $this->call(DeliverySeeder::class);
    }
}
