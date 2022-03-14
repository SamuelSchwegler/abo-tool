<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Order;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = now()->subWeeks(3);
        $end = $date->copy()->addMonths(4);

        while ($date->lte($end)) {
            $delivery = Delivery::create([
                'date' => $date,
                'deadline' => $date->copy()->subDays(2),
                'delivery_service_id' => DeliveryService::inRandomOrder()->first()->id,
            ]);

            foreach (Customer::all() as $customer) {
                Order::factory()->create([
                    'customer_id' => $customer->id,
                    'delivery_id' => $delivery->id,
                ]);
            }

            $date->addDays(7);
        }
    }
}
