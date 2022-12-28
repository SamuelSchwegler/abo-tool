<?php

namespace App\Jobs;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateReadyOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $delivieries = Delivery::where('date', '<=', now()->addDays(Order::PREVIEW_OFFSET_DAYS))
            ->where('approved', '=', 1)
            ->has('orders', '=', 0)->get();

        foreach ($delivieries as $deliviery) {
            DeliveryCreateOrders::dispatch($deliviery);
        }
    }
}
