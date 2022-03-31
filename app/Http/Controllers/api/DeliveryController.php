<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    public function deliveries() {
        return DeliveryResource::collection(Delivery::all());
    }
}
