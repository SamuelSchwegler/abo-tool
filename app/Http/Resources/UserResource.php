<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $customer = $this->customer;

        return [
            'id' => $this->id,
            'email' => $this->email,
            'customer' => ! is_null($customer) ? CustomerResource::make($customer) : [
                'delivery_address' => [
                    'street' => '',
                    'postcode' => '',
                    'city' => '',
                ],
                'billing_address' => [
                    'street' => '',
                    'postcode' => '',
                    'city' => '',
                ],
            ],
        ];
    }
}
