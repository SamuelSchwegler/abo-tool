<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function customer()
    {
        $customer = Auth::user()?->customer;

        return view('deliveries')->with([
            'next_deliveries' => $customer?->next_orders ?? []
        ]);
    }
}
