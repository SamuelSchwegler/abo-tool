<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Http\Resources\DeliveryServiceResource;
use App\Models\Delivery;
use App\Models\DeliveryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    public function deliveries(Request $request)
    {
        $validated = $request->validate([
            'start' => ['nullable', 'date_format:Y-m-d'],
            'delivery_service_ids' => ['nullable', 'array'],
            'delivery_service_ids.*' => ['required', 'exists:delivery_services,id'],
            'order_by' => ['nullable', Rule::in('date', 'deadline')]
        ]);

        $deliveries = Delivery::query();
        if (isset($validated['start'])) {
            $deliveries = $deliveries->where('date', '>=', $validated['start']);
        }
        if (isset($validated['delivery_service_ids'])) {
            $deliveries = $deliveries->whereIn('delivery_service_id', $validated['delivery_service_ids']);
        }

        if(isset($validated['order_by'])) {
            $deliveries = $deliveries->orderBy($validated['order_by']);
        } else {
            $deliveries = $deliveries->orderBy('date');
        }
        return response([
            'deliveries' => DeliveryResource::collection($deliveries->get()),
            'delivery_services' => DeliveryServiceResource::collection(DeliveryService::all())
        ]);
    }
}
