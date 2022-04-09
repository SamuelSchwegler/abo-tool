<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\DeliveryService;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $pickup = DeliveryService::create([
            'name' => 'Abholung in Hünibach',
            'pickup' => 1,
            'days' => ['sat']
        ]);

        $post = DeliveryService::create([
            'name' => 'Post',
            'days' => ['mon']
        ]);

        $velo = DeliveryService::create([
            'name' => 'Velokurier',
            'days' => ['tue']
        ]);

        DB::table('postcodes')->insert([
            ['delivery_service_id' => $velo->id, 'postcode' => 6122],
            ['delivery_service_id' => $velo->id, 'postcode' => 6130],
            ['delivery_service_id' => $velo->id, 'postcode' => 6125],
            ['delivery_service_id' => $velo->id, 'postcode' => 6124],
            ['delivery_service_id' => $post->id, 'postcode' => 3072],
            ['delivery_service_id' => $post->id, 'postcode' => 3000],
            ['delivery_service_id' => $post->id, 'postcode' => 3008],

        ]);

        Address::factory(20)->create();
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('customer');
            Customer::factory()->create([
                'user_id' => $user->id
            ]);
        });
        $admin = User::factory()->create([
            'email' => 'demo@webtheke.ch',
            'password' => bcrypt('1234'),
        ]);
        $admin->assignRole('admin');

        $customer = User::factory()->create([
            'email' => 'demo-user@webtheke.ch',
            'password' => bcrypt('Demo!User19*Test'),
        ]);
        $customer->assignRole('customer');

        Customer::factory()->create([
            'last_name' => 'Kunde',
            'user_id' => $customer->id,
        ]);
        Customer::factory()->create([
            'last_name' => 'Admin',
            'user_id' => $admin->id, // eigentlich nicht direkt benötigt
        ]);

        $this->call(HuenibachSeeder::class);
        $this->call(DeliverySeeder::class);
    }
}
