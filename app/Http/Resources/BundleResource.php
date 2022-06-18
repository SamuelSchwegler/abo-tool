<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
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
            'name' => $this->name,
            'formatted_price' => $this->formatted_price,
            'deliveries' => $this->deliveries,
            'price_per_delivery' => $this->price_per_delivery,
            'trial' => $this->trial,
            'short_description' => $this->short_description,
        ];
    }
}
