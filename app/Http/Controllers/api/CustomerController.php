<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use function PHPUnit\Framework\assertNotNull;

class CustomerController extends Controller
{
    public function customers(): Response
    {
        return response([
            'customers' => CustomerResource::collection(Customer::orderBy('last_name')->orderBy('first_name')->get())
        ]);
    }

    public function customer(Customer $customer): Response
    {
        return response([
            'customer' => CustomerResource::make($customer)
        ]);
    }

    public function update(Customer $customer, Request $request) {
        assertNotNull($customer);

        $rules = Customer::rules();
        unset($rules['delivery_address_id']);
        unset($rules['billing_address_id']);
        $validated = $request->validate($rules);

        $customer->update($validated);

        return \response([
            'msg' => 'ok',
            'customer' => CustomerResource::make($customer)
        ]);
    }
}
