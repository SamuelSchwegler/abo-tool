<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user;

        return [
            'id' => $this->id,
            'event' => __($this->event),
            'created_at' => [
                'd.m.Y H:i' => $this->created_at->format('d.m.Y H:i'),
            ],
            'user' => [
                'email' => $user->email,
            ],
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
        ];
    }
}
