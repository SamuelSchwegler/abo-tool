<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $balances = $this->productBalances();
        $buys = [];

        foreach($this->buys as $buy) {
            $buys[] = BuyResource::make($buy);
        }

        return [
            'id' => $this->id,
            'active' => $this->active,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'phone' => $this->phone,
            'email' => $this?->user?->email,
            'depository' => $this->depository,
            'delivery_address' => AddressResource::make($this->delivery_address),
            'billing_address' => AddressResource::make($this->billing_address),
            'delivery_option' => $this->delivery_option,
            'delivery_service' => DeliveryServiceResource::make($this->delivery_service()),
            'balances' => $balances,
            'buys' => $buys,
            'internal_comment' => $this->internal_comment,
            'discount' => $this->discount,
            'audits' => AuditResource::collection($this->audits()->orderBy('created_at', 'DESC')->get()),
        ];
    }
}
