<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    protected $guarded = ['id'];

    public function deliveryService() {
        return $this->belongsTo(DeliveryService::class);
    }
}
