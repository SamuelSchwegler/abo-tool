<?php

namespace Database\Seeders;

use App\Jobs\CreateOrdersForBuy;
use App\Models\Buy;
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
        Buy::factory(5)->create([
            'paid' => 0
        ]);
        $paidBuys = Buy::factory(5)->create([
            'paid' => 1
        ]);

        $date = now()->subWeeks(3);
        $end = $date->copy()->addMonths(3);

        while ($date->lte($end)) {
            $delivery1 = Delivery::create([
                'date' => $date,
                'deadline' => $date->copy()->subDays(1),
                'delivery_service_id' => DeliveryService::inRandomOrder()->first()->id,
            ]);

            $date->addDays(2);

            $delivery2 = Delivery::create([
                'date' => $date,
                'deadline' => $date->copy()->subDays(1),
                'delivery_service_id' => DeliveryService::inRandomOrder()->where('id', '!=', $delivery1->delivery_service_id)->first()->id,
            ]);

            $date->addDays(5);

        }

        foreach($paidBuys as $buy) {
            CreateOrdersForBuy::dispatch($buy);
        }
    }
}
