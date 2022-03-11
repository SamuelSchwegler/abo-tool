<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }

    public function next_orders(): HasMany {
        return $this->orders()->whereHas('delivery', function ($query) {
            $query->where('date', '<=', now()->addDays(Order::PREVIEW_OFFSET))->where('date', '>=', now());
        });
    }

    public function getNameAttribute(): string {
        return $this->first_name.' '.$this->last_name;
    }

    public function billing_address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }
}
