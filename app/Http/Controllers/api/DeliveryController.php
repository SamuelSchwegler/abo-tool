<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DeliveryException;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryResource;
use App\Http\Resources\DeliveryServiceResource;
use App\Models\Delivery;
use App\Models\DeliveryProductItem;
use App\Models\DeliveryService;
use App\Models\Item;
use App\Models\ItemOrigin;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        $deliveries = Delivery::whereDate('date', $date->format('Y-m-d'))->get();

        return response([
            'deliveries' => DeliveryResource::collection($deliveries),
            'delivery_services' => DeliveryServiceResource::collection(DeliveryService::all())
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
            'date' => ['nullable', 'date_format:d.m.Y'],
        ]);

        $delivery->update($validated);

        return \response(['msg' => 'ok', 'delivery' => DeliveryResource::make($delivery)]);
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

    /**
     * @param Delivery $delivery
     * @param Product $product
     * @param Request $request
     * @return Response|Application|ResponseFactory
     */
    public function addItem(Delivery $delivery, Product $product, Request $request): Response|Application|ResponseFactory
    {
        $item = Item::firstOrCreate(['name' => $request->item], ['item_origin_id' => ItemOrigin::first()->id]);
        DeliveryProductItem::firstOrCreate([
            'delivery_id' => $delivery->id,
            'product_id' => $product->id,
            'item_id' => $item->id
        ]);

        $delivery->refresh();

        return \response([
            'msg' => 'ok',
            'delivery' => DeliveryResource::make($delivery),
        ]);
    }

    /**
     * @param Delivery $delivery
     * @param Product $product
     * @param Item $item
     * @return Response|Application|ResponseFactory
     */
    public function removeItem(Delivery $delivery, Product $product, Item $item): Response|Application|ResponseFactory
    {
        DB::table('delivery_product_items')->where('delivery_id', $delivery->id)
            ->where('product_id', $product->id)->where('item_id', $item->id)->delete();
        $delivery->refresh();

        return \response([
            'msg' => 'ok',
            'delivery' => DeliveryResource::make($delivery),
        ]);
    }
}
