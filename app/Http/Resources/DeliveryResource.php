<?php

namespace App\Http\Resources;

use App\Models\DeliveryProductItem;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $orders = $this->active_orders()->with(['customer'])->get();
        $orders_array = [];
        $count = [];

        foreach ($orders as $order) {
            $product_id = $order->product->id;

            $orders_array[] = [
                'id' => $order->id,
                'customer' => [
                    'id' => $order->customer->id,
                    'name' => $order->customer->name,
                    'first_name' => $order->customer->first_name,
                    'last_name' => $order->customer->last_name,
                ],
                'product' => [
                    'id' => $product_id,
                    'name' => $order->product->name,
                ],
                'depository' => $this->depository ?? '',
            ];

            $count[$product_id] = (1 + (array_key_exists($product_id, $count) ? $count[$product_id] : 0));
        }

        usort($orders_array, function ($a, $b) {
            return strcmp($a['customer']['last_name'], $b['customer']['last_name']);
        });

        $summary = [];
        foreach ($count as $product_id => $c) {
            $product = Product::find($product_id);

            $summary[] = [
                'product_id' => $product_id,
                'name' => $product->name,
                'count' => $c,
            ];
        }

        return [
            'id' => $this->id,
            'date' => [
                'd.m.Y' => $this->date->format('d.m.Y'),
                'Y-m-d' => $this->date->format('Y-m-d'),
                'weekday' => $this->date->isoFormat('dddd'),
                'passed' => today()->gt($this->date),
            ],
            'deadline' => [
                'd.m.Y' => $this->deadline->format('d.m.Y'),
                'Y-m-d' => $this->deadline->format('Y-m-d'),
            ],
            'delivery_service' => [
                'id' => $this->delivery_service->id,
                'name' => $this->delivery_service->name,
            ],
            'approved' => ($this->approved === 1),
            'orders' => $orders_array,
            'items' => $this->items($count),
            'summary' => $summary,
        ];
    }

    public function items(array $situation): array
    {
        $items_array = [];
        foreach ($situation as $product_id => $amount) {
            $product = Product::find($product_id);
            $items_array[$product_id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'items' => [],
                'synced' => false,
                'dateFormat' => [
                    'd.m.Y' => $this->date->format('d.m.Y'),
                    'Y-m-d' => $this->date->format('Y-m-d'),
                ],
            ];

            $pivots = DeliveryProductItem::where('delivery_id', $this->id)
                ->where('product_id', $product->id)->get();

            if ($pivots->count() === 0) {
                $pivots = DeliveryProductItem::whereDate('date', $this->date->format('Y-m-d'))
                    ->where('product_id', $product->id)->get();

                $items_array[$product_id]['synced'] = true;
            }

            foreach ($pivots as $pivot) {
                $items_array[$pivot->product_id]['items'][] = ItemResource::make($pivot->item);
            }
        }

        return $items_array;
    }
}
