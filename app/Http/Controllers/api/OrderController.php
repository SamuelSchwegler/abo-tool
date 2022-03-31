<?php

namespace App\Http\Controllers\api;

use App\Exceptions\OrderEditException;
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
use function PHPUnit\Framework\assertNotNull;
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
            'orders' => OrderResource::collection(OrderResource::collection($customer?->next_orders ?? collect([]))),
            'product_balances' => $customer->productBalances()
        ]);
    }

    /**
     * @param Order $order
     * @param Request $request
     * @return Response|Application|ResponseFactory
     * @throws OrderEditException
     */
    public function update(Order $order, Request $request): Response|Application|ResponseFactory
    {
        assertNotNull($order);
        if ($order->deadlinePassed()) {
            throw OrderEditException::deadlineHasPassed($order);
        }

        $validated = $request->validate([
            'depository' => ['nullable', 'string']
        ]);
        $order->update($validated);

        return response(['order' => OrderResource::make($order)]);
    }

    /**
     * Abmeldung einer Bestellung regeln
     * @throws OrderEditException
     */
    public function toggleCancel(Order $order): Response|Application|ResponseFactory
    {
        if ($order->deadlinePassed()) {
            throw OrderEditException::deadlineHasPassed($order);
        }
        $order->update([
            'canceled' => !$order->canceled
        ]);

        return response(['order' => OrderResource::make($order)]);
    }
}
