<?php

namespace App\Jobs;

use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliveryCreateOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Delivery $delivery;
    protected DeliveryService $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Delivery $delivery)
    {
        $this->delivery = $delivery;
        $this->service = $delivery->delivery_service;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $customers = $this->service->customers();
        foreach ($customers as $customer) {

            foreach ($customer->buys as $buy) {
                $order = Order::firstOrCreate([
                    'customer_id' => $customer->id,
                    'delivery_id' => $this->delivery->id,
                    'product_id' => $buy->bundle->product->id,
                ]);
            }

        }
    }
}
