<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\CustomerDelivery;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function customer()
    {
        $customer = Auth::user()?->customer;

        return view('deliveries')->with([
            'next_deliveries' => $customer?->next_customer_deliveries ?? []
        ]);
    }
}
