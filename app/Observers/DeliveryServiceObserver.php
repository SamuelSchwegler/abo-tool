<?php

namespace App\Observers;

use App\Models\DeliveryService;

class DeliveryServiceObserver
{
    public function created(DeliveryService $deliveryService): void
    {

    }

    public function updated(DeliveryService $deliveryService): void
    {
        if($deliveryService->isDirty('pickup') && $deliveryService->pickup) {
            $deliveryService->postcodes()->delete();
        }
    }
}
