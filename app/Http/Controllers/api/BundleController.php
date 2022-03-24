<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Http\Resources\BuyResource;
use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\SendInvoice;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\assertNotNull;

class BundleController extends Controller
{
    public function bundles()
    {
        return BundleResource::collection(Bundle::all());
    }

    public function bundle(Bundle $bundle)
    {
        return BundleResource::make($bundle);
    }

    public function submitBuy(Bundle $bundle, Request $request) {
        Log::info($request->all());

        if(Auth::check()) {
            $user = Auth::user();
            $customer = $user->customer;
        } else {
            $userValidated = $request->validate([
                'email' => ['required', 'string'],
                'password' => ['required', 'string']
            ]);

            $user = User::create([
                'email' => $userValidated['email'],
                'password' => Hash::make($userValidated['password'])
            ]);


        }

        assertNotNull($user);

        $customerValidated = $request->validate([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'company_name' => ['nullable', 'string'],
            'phone' => ['required', 'string']
        ]);

        if(is_null($customer)) {
            $customer = Customer::create(array_merge([
                'user_id' => $user->id
            ], $customerValidated));
        } else {
            $customer->update($customerValidated);
        }

        $buy = Buy::create([
            'customer_id' => $customer->id,
            'bundle_id' => $bundle->id,
            'price' => $bundle->price,
            'issued' => now()
        ]);

        $user->notify(new SendInvoice($buy));

        return response(['status' => 'success', 'buy' => BuyResource::make($buy)]);
    }
}
