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
        $deliveries = Delivery::where('date', '>=', now()->format('Y-m-d'))
            ->where('date', '<=', now()->addDays(Order::PREVIEW_OFFSET_DAYS)->format('Y-m-d'))
            ->where('approved', '=', 1)->orderBy('date')->get();

        foreach ($deliveries as $delivery) {
            DeliveryCreateOrders::dispatch($delivery);
        }
    }
}
