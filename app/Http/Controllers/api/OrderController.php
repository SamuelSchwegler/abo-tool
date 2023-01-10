<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DeliveryException;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\OrderResource;
use App\Jobs\CreateOrders;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use function response;

class OrderController extends Controller
{
    public function orders(?Customer $customer = null): Response|Application|ResponseFactory
    {
        $user = Auth::user();
        if(is_null($customer)) {
            $customer = $user->customer;
        }

        if($user->can('manage deliveries')) {
            $orders = $customer?->next_orders(now()->subWeeks(4), 365)->get();
        } else {
            $orders = $customer?->next_orders(now()->subWeek())->get();
        }

        return \response([
            'customer' => CustomerResource::make($customer),
            'orders' => OrderResource::collection($orders ?? collect([])),
            'product_balances' => $customer?->productBalances() ?? [],
            'distinct_delivery_services' => $orders->groupBy('delivery.delivery_service.id')->map->count()->count(),
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
        if (! \auth()->user()->can('manage customers') && $order->deadlinePassed()) {
            throw DeliveryException::deadlineHasPassed($order->delivery);
        }

        $validated = $request->validate([
            'depository' => ['nullable', 'string'],
            'internal_comment' => ['nullable', 'string'],
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
        if (! \auth()->user()->can('manage customers') && $order->deadlinePassed()) {
            throw DeliveryException::deadlineHasPassed($order->delivery);
        }

        $order->update([
            'canceled' => ! $order->canceled,
        ]);

        // falls abgemeldet neue Lieferung erstellen
        CreateOrders::dispatchSync($order->customer, $order->product);

        return response(['order' => OrderResource::make($order)]);
    }

    public function delete(Order $order): Response|Application|ResponseFactory
    {
        $order->delete();

        return response(['msg' => 'ok']);
    }
}
