<?php

namespace App\Observers;

use App\Models\Buy;

class BuyObserver
{
    public function created(Buy $buy) {
        if(is_null($buy->discount) && $buy->customer->discount > 0) {
            $buy->discount = $buy->customer->discount;
            $buy->saveQuietly();
        }
    }
}
