<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\Buy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyController extends Controller
{
    public function contact(Bundle $bundle)
    {
        $user = Auth::user();
        if (is_null($user)) {
            // todo login
        } else {
            $customer = $user->customer;

            $buy = Buy::create([
                'customer_id' => $customer->id,
                'bundle_id' => $bundle->id
            ]);

            return view('buy.contact')->with([
                'bundle' => $bundle,
                'customer' => $buy->customer
            ]);
        }


    }

    public function contactSubmit()
    {
        // todo allenfalls konto erstellen
    }

    public function payment()
    {

    }

    public function confirmation()
    {

    }
}
