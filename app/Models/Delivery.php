<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $dates = ['date', 'deadline'];

    public function delivery_service(): BelongsTo
    {
        return $this->belongsTo(DeliveryService::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
