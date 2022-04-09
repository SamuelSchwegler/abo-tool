<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

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
}
