<?php

namespace App\Observers;

use App\Jobs\CreateOrdersForDelivery;
use App\Models\Delivery;
use Illuminate\Support\Facades\Log;

class DeliveryObserver
{
    /**
     * Handle the Delivery "created" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function created(Delivery $delivery)
    {
        //
    }

    /**
     * Handle the Delivery "updated" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function updated(Delivery $delivery)
    {
        if($delivery->isDirty('approved') && $delivery->approved) {
            CreateOrdersForDelivery::dispatch($delivery);
        }
    }

    /**
     * Handle the Delivery "deleted" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function deleted(Delivery $delivery)
    {
        //
    }

    /**
     * Handle the Delivery "restored" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function restored(Delivery $delivery)
    {
        //
    }

    /**
     * Handle the Delivery "force deleted" event.
     *
     * @param  \App\Models\Delivery  $delivery
     * @return void
     */
    public function forceDeleted(Delivery $delivery)
    {
        //
    }
}
