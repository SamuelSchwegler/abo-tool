<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }

    public function next_orders(): HasMany {
        return $this->orders()->whereHas('delivery', function ($query) {
            $query->where('date', '<=', now()->addDays(Order::PREVIEW_OFFSET))->where('date', '>=', now());
        });
    }
}
