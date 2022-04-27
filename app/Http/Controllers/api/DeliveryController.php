<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DeliveryException;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Http\Resources\DeliveryServiceResource;
use App\Models\Delivery;
use App\Models\DeliveryService;
use App\Models\Item;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    public function deliveries(Request $request)
    {
        $validated = $request->validate([
            'start' => ['nullable', 'date_format:Y-m-d'],
            'delivery_service_ids' => ['nullable', 'array'],
            'delivery_service_ids.*' => ['required', 'exists:delivery_services,id'],
            'order_by' => ['nullable', Rule::in('date', 'deadline')],
        ]);

        $deliveries = Delivery::query();
        if (isset($validated['start'])) {
            $deliveries = $deliveries->where('date', '>=', $validated['start']);
        }
        if (isset($validated['delivery_service_ids'])) {
            $deliveries = $deliveries->whereIn('delivery_service_id', $validated['delivery_service_ids']);
        }

        if (isset($validated['order_by'])) {
            $deliveries = $deliveries->orderBy($validated['order_by']);
        } else {
            $deliveries = $deliveries->orderBy('date');
        }

        return response([
            'deliveries' => DeliveryResource::collection($deliveries->get()),
            'delivery_services' => DeliveryServiceResource::collection(DeliveryService::all()),
        ]);
    }

    public function delivery(Delivery $delivery)
    {
        return response([
            'delivery' => DeliveryResource::make($delivery),
        ]);
    }

    public function update(Delivery $delivery, Request $request)
    {
        if ($delivery->deadlinePassed()) {
            throw DeliveryException::deadlineHasPassed($delivery);
        }

        $validated = $request->validate([
            'date' => ['nullable', 'date_format:d.m.Y']
        ]);

        $delivery->update($validated);

        return \response(['msg' => 'ok', 'delivery' => DeliveryResource::make($delivery)]);
    }

    /**
     * @param Delivery $delivery
     * @return Response|Application|ResponseFactory
     *
     * @throws DeliveryException
     */
    public function toggleApproved(Delivery $delivery): Response|Application|ResponseFactory
    {
        if ($delivery->deadlinePassed()) {
            throw DeliveryException::deadlineHasPassed($delivery);
        }

        $delivery->update([
            'approved' => !$delivery->approved,
        ]);

        return $this->delivery($delivery);
    }

    public function addItem(Delivery $delivery, Item $item): Response|Application|ResponseFactory
    {
        $delivery->items()->attach($item->id);

        return \response([
            'msg' => 'ok',
            'delivery' => DeliveryResource::make($delivery),
        ]);
    }
}
