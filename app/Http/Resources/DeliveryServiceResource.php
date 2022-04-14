<?php

namespace App\Http\Resources;

use App\Models\Delivery;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable
    {
        $unapproved_deliveries = $this->deliveries()->where(function($query) {
            return $query->where('approved', 0)->orWhere('updated_at', '>=', now()->subDay());
        })->get();
        $unapproved_array = [];
        foreach($unapproved_deliveries as $delivery) {
            $unapproved_array[] = [
                'id' => $delivery->id,
                'date' => [
                    'd.m.Y' => $delivery->date->format('d.m.Y'),
                    'Y-m-d' => $delivery->date->format('Y-m-d')
                ],
                'deadline' => [
                    'd.m.Y' => $delivery->deadline->format('d.m.Y'),
                    'Y-m-d' => $delivery->deadline->format('Y-m-d')
                ],
                'approved' => ($delivery->approved === 1)
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'postcodes' => $this->postcodes,
            'days' => $this->days,
            'pickup' => ($this->pickup === 1),
            'deadline_distance' => $this->deadline_distance,
            'unapproved_deliveries' => $unapproved_array,
            'delivery_cost' => $this->delivery_cost
        ];
    }
}
