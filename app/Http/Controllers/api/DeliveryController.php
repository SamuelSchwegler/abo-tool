<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DeliveryException;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Http\Resources\DeliveryServiceResource;
use App\Models\Delivery;
use App\Models\DeliveryProductItem;
use App\Models\DeliveryService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    public function deliveries(Request $request): Response
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

    public function deliveriesForDate(string $date, Request $request): Response {
        $date = Carbon::parse($date);

        $deliveries = Delivery::whereDate('date', $date->format('Y-m-d'))->get()->sort(function ($a, $b) {
            return strcasecmp($a->delivery_service->name, $b->delivery_service->name);
        });

        $items = [];

        $products = DB::table('products')
            ->join('orders', 'orders.product_id', '=', 'products.id')
            ->join('deliveries', 'orders.delivery_id', '=', 'deliveries.id')
            ->whereIn('deliveries.id', $deliveries->pluck('id')->toArray())
            ->select('products.*')
            ->groupBy('products.id')->get();

        foreach($products as $product) {
            $items[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'items' => [],
                'dateFormat' => [
                    'd.m.Y' => $date->format('d.m.Y'),
                    'Y-m-d' => $date->format('Y-m-d'),
                ],
            ];
        }

        $dpis = DeliveryProductItem::whereNull('delivery_id')->whereDate('date', $date->format('Y-m-d'))->get();
        foreach($dpis as $dpi) {
            $items[$dpi->product_id]['items'][] = [
                'id' => $dpi->item->id,
                'name' => $dpi->item->name,
            ];
        }

        return response([
            'deliveries' => DeliveryResource::collection($deliveries),
            'delivery_services' => DeliveryServiceResource::collection(DeliveryService::all()),
            'items' => $items,
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
        if (! Auth::user()->can('manage deliveries') && $delivery->deadlinePassed()) {
            throw DeliveryException::deadlineHasPassed($delivery);
        }

        $validated = $request->validate([
            'date' => ['nullable', 'date_format:d.m.Y'],
        ]);

        if(! is_null($validated['date'])) {
            $validated['deadline'] = Carbon::parse($validated['date'])->subDays($delivery->delivery_service->deadline_distance)->format('Y-m-d');
        }

        $delivery->update($validated);

        return \response([
            'msg' => 'ok',
            'delivery' => DeliveryResource::make($delivery),
            'delivery_service' => DeliveryServiceResource::make($delivery->delivery_service),
        ]);
    }

    /**
     * @param  Delivery  $delivery
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
            'approved' => ! $delivery->approved,
        ]);

        return $this->delivery($delivery);
    }
}
