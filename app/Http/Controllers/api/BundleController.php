<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Http\Resources\BuyResource;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\Bundle;
use App\Models\Buy;
use App\Models\Customer;
use App\Models\DeliveryService;
use App\Models\User;
use App\Notifications\SendInvoice;
use App\Rules\DeliveryPossibleToPostcode;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Mailer\Exception\TransportException;

class BundleController extends Controller
{
    public function bundles()
    {
        return BundleResource::collection(Bundle::orderBy('order')->get());
    }

    public function bundle(Bundle $bundle)
    {
        return BundleResource::make($bundle);
    }

    public function update(Bundle $bundle, Request $request): response
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
        ]);

        $bundle->update($validated);

        return response([
            'bundle' => BundleResource::make($bundle),
        ]);
    }

    /**
     * Zahlung mit allen Daten absenden.
     *
     * @param  Bundle  $bundle
     * @param  Request  $request
     * @return Application|ResponseFactory|Response
     */
    public function submitBuy(Bundle $bundle, Request $request)
    {
        $customer = null;
        if (Auth::check()) {
            $user = Auth::user();
            $customer = $user->customer;
        } else {
            $userValidated = $request->validate([
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string'],
            ]);

            $user = User::create([
                'email' => $userValidated['email'],
                'password' => Hash::make($userValidated['password']),
            ])->syncRoles(['customer']);

            Auth::login($user);
        }

        //$customerValidated = $request->validate(Customer::rules());
        $validator = Validator::make($request->all(), Customer::rules());
        if ($validator->fails()) {
            return \response([
                'errors' => $validator->errors()->toArray(),
                'authenticated' => ! Auth::guest(),
                'user' => UserResource::make(Auth::user()),
            ], 422);
        }
        $customerValidated = $validator->validated();

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

        if (is_null($customer)) {
            $customer = Customer::create(array_merge([
                'user_id' => $user->id,
            ], $customerValidated, $address_data));
        } else {
            $customer->delivery_address?->delete();
            $customer->billing_address?->delete();

            $customer->update(array_merge($customerValidated, $address_data));
        }
        $user->refresh(); // customer für user nachladen

        if ($request->delivery_option === 'pickup') {
            if (! isset($request->delivery_service['id'])) {
                return abort(422);
            }
            $service = DeliveryService::find($request->delivery_service['id']);
            $customer->update([
                'delivery_service_id' => $service->id,
            ]);
        } else {
            if (! is_null($customer->delivery_service_id)) {
                $customer->update([
                    'delivery_service_id' => null,
                ]);
            }
            $service = $customer->delivery_service();
        }

        $buy = Buy::create([
            'customer_id' => $customer->id,
            'bundle_id' => $bundle->id,
            'price' => $bundle->price,
            'issued' => now(),
            'delivery_cost' => $service->delivery_cost * $bundle->deliveries,
        ]);

        try {
            $user->notify(new SendInvoice($buy));
        } catch (TransportException $exception) {
            Log::error($exception->getMessage()); // zbsp falls Mailserver nicht will
        }

        return response(['status' => 'success', 'buy' => BuyResource::make($buy)]);
    }
}
