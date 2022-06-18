<?php

namespace App\Console\Commands;

use App\Jobs\DeliveryCreateOrders;
use App\Models\Delivery;
use Illuminate\Console\Command;

class SetupDeliveries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:deliveries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (Delivery::all() as $d) {
            if($d->approved) {
                $this->info('lieferungen für '.$d->date->format('d.m.Y').' mit '.$d->delivery_service->name.' erstellen');
                DeliveryCreateOrders::dispatch($d);
            }

        }

        return 0;
    }
}
