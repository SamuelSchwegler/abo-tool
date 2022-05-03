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
        $user = [];
        if(!is_null($this->customer->user)) {
            $user = [
                'email' => $this->customer->user->email,
            ];
        }

        return [
            'id' => $this->id,
            'bill_number' => $this->bill_number,
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'user' => $user,
            ],
            'price' => $this->formated_price,
            'delivery_cost' => $this->delivery_cost,
            'total_cost' => $this->total_cost,
            'paid' => $this->paid === 1,
            'created' => [
                'd.m.Y' => $this->issued->format('d.m.Y'),
            ],
            'age' => $this->issued->diffInDays(now()) + 1,
            'bundle' => BundleResource::make($this->bundle),
        ];
    }
}
