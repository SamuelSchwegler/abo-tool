<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Jobs\CreateOrders;
use App\Models\Address;
use App\Models\Customer;
use App\Models\DeliveryService;
use App\Models\Product;
use App\Models\User;
use App\Rules\DeliveryPossibleToPostcode;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    public function customers(): Response
    {
        return response([
            'customers' => CustomerResource::collection(Customer::orderBy('last_name')->orderBy('first_name')->get()),
        ]);
    }

    public function customer(Customer $customer): Response
    {
        return response([
            'customer' => CustomerResource::make($customer),
            'user' => ! is_null($customer->user) ? UserResource::make($customer->user) : null,
        ]);
    }

    public function store(Request $request): Response|Application|ResponseFactory
    {
        $customerValidated = $request->validate(Customer::rules() + [
            'email' => ['nullable', 'email', 'unique:users,email'],
        ]);
        $customer = Customer::create($customerValidated);

        if (! is_null($customerValidated['email'])) {
            $user = User::create([
                'email' => $customerValidated['email'],
                'password' => 'keinPasswort',
            ])->syncRoles(['customer']);
            $customer->update([
                'user_id' => $user->id,
            ]);
        }

        return \response([
            'msg' => 'ok',
            'customer' => CustomerResource::make($customer),
        ]);
    }

    public function update(Customer $customer, Request $request): Response|Application|ResponseFactory
    {
        $customerValidated = $request->validate(Customer::rules());
        $deliveryAddressRules = Address::rules('delivery_address');
        $billingAddressRules = Address::rules('billing_address');
        $address_data = [];

        switch ($request->delivery_option) {
            case 'match':
                $deliveryAddressRules['delivery_address.postcode'][] = new DeliveryPossibleToPostcode();
                $deliveryAddressValidated = $request->validate($deliveryAddressRules, Address::messages('delivery_address'));

                $address = Address::create($deliveryAddressValidated['delivery_address']);

                $address_data = [
                    'billing_address_id' => $address->id,
                    'delivery_address_id' => $address->id,
                ];
                break;
            case 'pickup':
                $billingAddressValidated = $request->validate($billingAddressRules, Address::messages('billing_address'));
                $address = Address::create($billingAddressValidated['billing_address']);

                $address_data = [
                    'billing_address_id' => $address->id,
                    'delivery_address_id' => null,
                ];
                break;
            case 'split':
                $deliveryAddressRules['delivery_address.postcode'][] = new DeliveryPossibleToPostcode();
                $deliveryAddressValidated = $request->validate($deliveryAddressRules, Address::messages('delivery_address'));
                $billingAddressValidated = $request->validate($billingAddressRules, Address::messages('billing_address'));

                $delivery = Address::create($deliveryAddressValidated['delivery_address']);
                $billing = Address::create($billingAddressValidated['billing_address']);

                $address_data = [
                    'delivery_address_id' => $delivery->id,
                    'billing_address_id' => $billing->id,
                ];
        }

        $customer->delivery_address?->delete();
        $customer->billing_address?->delete();

        $customer->update(array_merge($customerValidated, $address_data));
        $customer->refresh();

        if ($request->delivery_option === 'pickup') {
            if(! isset($request->delivery_service['id'])) {
                return abort(422);
            }
            $service = DeliveryService::find($request->delivery_service['id']);
            $customer->update([
                'delivery_service_id' => $service->id,
            ]);
        } else {
            $customer->update([
                'delivery_service_id' => null,
            ]);
        }

        return response([
            'msg' => 'ok',
            'customer' => CustomerResource::make($customer),
            'user' => UserResource::make($customer->user),
        ]);
    }

    /**
     * @param  Customer  $customer
     * @param  Request  $request
     * @return Response
     * @changes v0.1.2 - user orders überschrieben alle Produkte
     */
    public function updateUsedOrders(Customer $customer, Request $request): Response
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'used_orders' => ['required', 'numeric'],
        ], [
            'used_orders.required' => 'Geben Sie eine Zahl an (kann auch 0 sein)',
        ]);

        $product = Product::find($validated['product_id']);

        $used_orders = $customer->used_orders;
        $used_orders[$product->id] = $validated['used_orders'];

        $customer->update([
            'used_orders' => $used_orders,
        ]);

        CreateOrders::dispatchSync($customer, $product);

        return \response([
            'msg' => 'ok',
            'product_balances' => $customer->productBalances(),
        ]);
    }
}
