<?php

namespace Database\Seeders;

use App\Jobs\CreateOrdersForBuy;
use App\Models\Buy;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Item;
use App\Models\ItemOrigin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ItemOrigin::factory()->count(3)->create();
        Item::factory()->count(10)->create();

        Buy::factory(5)->create([
            'paid' => 0,
        ]);
        $paidBuys = Buy::factory(5)->create([
            'paid' => 1,
        ]);

        $date = now()->subWeeks(3);
        $end = $date->copy()->addMonths(3);

        while ($date->lte($end)) {
            foreach (DeliveryService::all() as $service) {
                if (in_array(strtolower($date->locale('en')->isoFormat('ddd')), $service->days)) {
                    $delivery = Delivery::create([
                        'date' => $date,
                        'deadline' => $date->copy()->subDays($service->deadline_distance),
                        'delivery_service_id' => $service->id,
                        'approved' => $date->lt(now()->addWeeks(2)),
                        'updated_at' => $date->copy()->subDays(15),
                    ]);

                    foreach (Item::inRandomOrder()->take(5)->get() as $item) {
                        DB::table('delivery_item')->insert([
                            'delivery_id' => $delivery->id,
                            'item_id' => $item->id,
                        ]);
                    }
                }
            }

            $date->addDay();
        }

        foreach ($paidBuys as $buy) {
            CreateOrdersForBuy::dispatch($buy);
        }
    }
}
