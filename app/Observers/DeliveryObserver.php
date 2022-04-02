<?php

namespace App\Observers;

use App\Jobs\DeliveryCreateOrders;
use App\Jobs\DeliveryRemoveOrders;
use App\Models\Delivery;

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
        if($delivery->isDirty('approved')) {
            if($delivery->approved) {
                DeliveryCreateOrders::dispatch($delivery);
            } else {
                DeliveryRemoveOrders::dispatch($delivery);
            }
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
