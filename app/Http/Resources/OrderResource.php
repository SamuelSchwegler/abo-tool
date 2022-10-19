<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
                'delivery_service' => [
                    'id' => $delivery->delivery_service->id,
                    'name' => $delivery->delivery_service->name
                ]
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
            ],
            'depository' => $this->depository ?? '',
            'internal_comment' => $this->internal_comment ?? '', // müsste allenfalls geschützt sein?
            'canceled' => $this->canceled,
            'affordable' => $this->affordable,
            'deadline_passed' => $this->deadlinePassed(),
            'audits' => AuditResource::collection($this->audits)
        ];
    }
}
