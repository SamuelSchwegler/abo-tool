<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bundle extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'trial' => 'boolean'
    ];

    public function buys(): HasMany
    {
        return $this->hasMany(Buy::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, '.', '\'');
    }

    public function getPricePerDeliveryAttribute(): string
    {
        return number_format($this->price / $this->deliveries, 2, '.', '\'');
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value / 100,
            set: fn($value) => $value * 100,
        );
    }
}
