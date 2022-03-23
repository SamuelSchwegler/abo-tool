<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $delivery = $this->delivery;
        $product = $this->product;

        return [
            'id' => $this->id,
            'delivery' => [
                'id' => $delivery->id,
                'date' => [
                    'd.m.Y' => $delivery->date->format('d.m.Y'),
                ],
                'deadline' => [
                    'd.m.Y' => $delivery->deadline->format('d.m.Y'),
                ],
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->name
            ],
            'depository' => $this->depository ?? '',
            'canceled' => $this->canceled === 1,
            'deadline_passed' => $this->deadlinePassed()
        ];
    }
}
