<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BuyResource extends JsonResource
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
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name
            ],
            'price' => $this->formated_price,
            'paid' => $this->paid === 1,
            'created' => [
                'd.m.Y' => $this->issued->format('d.m.Y')
            ],
            'age' => $this->issued->diffInDays(now()) + 1
        ];
    }
}
