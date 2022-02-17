<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        while($date->lte($end)) {
            Delivery::create([
                'date' => $date,
                'deadline' => $date->copy()->subDays(2)
            ]);

            $date->addDays(7);
        }
    }
}
