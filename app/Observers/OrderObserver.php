<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $order) {
        $order->depository = $order->customer->depository;
        $order->saveQuietly();
    }
}
