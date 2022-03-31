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
        return [
            'id' => $this->id,
            'date' => [
                'd.m.Y' => $this->date->format('d.m.Y'),
                'Y-m-d' => $this->date->format('Y-m-d'),
                'weekday' => $this->date->isoFormat('dddd')
            ],
            'deadline' => [
                'd.m.Y' => $this->deadline->format('d.m.Y'),
                'Y-m-d' => $this->deadline->format('Y-m-d')
            ],
            'delivery_service' => [
                'id' => $this->delivery_service->id,
                'name' => $this->delivery_service->name
            ],
            'orders' => OrderResource::collection($this->orders)
        ];
    }
}
