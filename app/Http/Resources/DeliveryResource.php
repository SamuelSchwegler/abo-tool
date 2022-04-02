<?php

namespace App\Http\Resources;

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
        $orders = $this->orders;
        $orders_array = [];
        foreach($orders as $order) {
            $orders_array[] = [
                'id' => $order->id,
                'customer' => [
                    'id' => $order->customer->id,
                    'name' => $order->customer->name
                ],
                'product' => [
                    'id' => $order->product->id,
                    'name' => $order->product->name
                ],
                'depository' => $this->depository ?? '',
            ];
        }

        return [
            'id' => $this->id,
            'date' => [
                'd.m.Y' => $this->date->format('d.m.Y'),
                'Y-m-d' => $this->date->format('Y-m-d'),
                'weekday' => $this->date->isoFormat('dddd'),
                'passed' => today()->gt($this->date)
            ],
            'deadline' => [
                'd.m.Y' => $this->deadline->format('d.m.Y'),
                'Y-m-d' => $this->deadline->format('Y-m-d')
            ],
            'delivery_service' => [
                'id' => $this->delivery_service->id,
                'name' => $this->delivery_service->name
            ],
            'approved' => ($this->approved === 1),
            'orders' => $orders_array
        ];
    }
}
