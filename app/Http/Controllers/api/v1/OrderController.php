<?php

namespace App\Http\Controllers\api\v1;

use App\Exceptions\OrderEditException;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Abmeldung einer Bestellung regeln
     * @throws OrderEditException
     */
    public function toggleCancel(Order $order): Response|Application|ResponseFactory
    {
        if($order->deadlinePassed()) {
            Log::info('passed');
            throw OrderEditException::deadlineHasPassed($order);
        }
        $order->update([
            'canceled' => !$order->canceled
        ]);

        return response(['order' => OrderResource::make($order)]);
    }
}
