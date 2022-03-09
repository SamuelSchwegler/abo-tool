<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryService extends Model
{
    protected $guarded = ['id'];

    public function postcodes() {
        return $this->hasMany(Postcode::class)->orderBy('postcode');
    }
}
