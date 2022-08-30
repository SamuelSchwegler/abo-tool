<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
            ->has('orders', '=', 0)->get();

        foreach ($delivieries as $deliviery) {
            DeliveryCreateOrders::dispatch($deliviery);
        }
    }
}
