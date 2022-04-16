<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Address;
use App\Models\Customer;
use App\Rules\DeliveryPossibleToPostcode;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
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

    public function update(Customer $customer, Request $request): Response|Application|ResponseFactory
    {
        Log::info($request->all());
        $customerValidated = $request->validate(Customer::rules());
        $deliveryAddressRules = Address::rules('delivery_address');
        $billingAddressRules = Address::rules('billing_address');
        $address_data = [];

        switch ($request->delivery_option) {
            case "match":
                $deliveryAddressRules['delivery_address.postcode'][] = new DeliveryPossibleToPostcode();
                $deliveryAddressValidated = $request->validate($deliveryAddressRules);

                $address = Address::create($deliveryAddressValidated['delivery_address']);

                $address_data = [
                    'billing_address_id' => $address->id,
                    'delivery_address_id' => $address->id
                ];
                break;
            case "pickup":
                $billingAddressValidated = $request->validate($billingAddressRules);
                $address = Address::create($billingAddressValidated['billing_address']);

                $address_data = [
                    'billing_address_id' => $address->id,
                    'delivery_address_id' => null,
                ];
                break;
            case "split":
                $deliveryAddressRules['delivery_address.postcode'][] = new DeliveryPossibleToPostcode();
                $deliveryAddressValidated = $request->validate($deliveryAddressRules);
                $billingAddressValidated = $request->validate($billingAddressRules);

                Log::info($billingAddressValidated);

                $delivery = Address::create($deliveryAddressValidated['delivery_address']);
                $billing = Address::create($billingAddressValidated['billing_address']);
                Log::info($billing);

                $address_data = [
                    'delivery_address_id' => $delivery->id,
                    'billing_address_id' => $billing->id
                ];
        }

        $customer->delivery_address?->delete();
        $customer->billing_address?->delete();

        $customer->update(array_merge($customerValidated, $address_data));
        $customer->refresh();

        return response([
            'msg' => 'ok',
            'customer' => CustomerResource::make($customer)
        ]);
    }
}
