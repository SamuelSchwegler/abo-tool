<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DeliveryException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function response;

class OrderController extends Controller
{
    public function orders(?Customer $customer = null)
    {
        if(is_null($customer)) {
            $user = Auth::user();
            $customer = $user->customer;
        }

        return \response([
            'customer' => CustomerResource::make($customer),
            'orders' => OrderResource::collection($customer?->next_orders ?? collect([])),
            'product_balances' => $customer?->productBalances() ?? [],
        ]);
    }

    /**
     * @param  Order  $order
     * @param  Request  $request
     * @return Response|Application|ResponseFactory
     *
     * @throws DeliveryException
     */
    public function update(Order $order, Request $request): Response|Application|ResponseFactory
    {
        if ($order->deadlinePassed()) {
            throw DeliveryException::deadlineHasPassed($order->delivery);
        }

        $validated = $request->validate([
            'depository' => ['nullable', 'string'],
            'internal_comment' => ['nullable', 'string']
        ]);
        $order->update($validated);

        return response(['order' => OrderResource::make($order)]);
    }

    /**
     * Abmeldung einer Bestellung regeln.
     *
     * @throws DeliveryException
     */
    public function toggleCancel(Order $order): Response|Application|ResponseFactory
    {
        if (!\auth()->user()->can('manage customers') && $order->deadlinePassed()) {
            throw DeliveryException::deadlineHasPassed($order->delivery);
        }

        Log::info($order->canceled);
        $order->update([
            'canceled' => $order->canceled ? false : true
        ]);
        Log::info($order->canceled);

        return response(['order' => OrderResource::make($order)]);
    }
}
