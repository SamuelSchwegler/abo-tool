<?php

namespace App\Jobs;

use App\Models\Delivery;
use App\Models\DeliveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateDeliveries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const DISTANCE = 120;

    protected ?DeliveryService $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(?DeliveryService $service = null)
    {
        $this->service = $service;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(is_null($this->service)) {
            foreach (DeliveryService::all() as $service) {
                $this->createDeliveriesForService($service);
            }
        } else {
            $this->createDeliveriesForService($this->service);
        }

    }

    private function createDeliveriesForService(DeliveryService $service) {
        $generate_until = now()->addDays(self::DISTANCE)->endOfDay();

        $max = $service->deliveries->max('date');
        if(is_null($max) || $max->lt($generate_until)) {
            // maybe there are missing days
            $date = is_null($max) ? now()->addDays($service->deadline_distance) : $max->addDay()->copy();

            while($date->lt($generate_until)) {
                if(in_array(strtolower($date->locale('en')->isoFormat('ddd')), $service->days)) {
                    $delivery = Delivery::create([
                        'date' => $date,
                        'deadline' => $date->copy()->subDays($service->deadline_distance),
                        'delivery_service_id' => $service->id,
                    ]);
                }

                $date->addDay();
            }
        }
    }
}
