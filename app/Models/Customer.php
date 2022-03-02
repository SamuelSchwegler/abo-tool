<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    public function customer_deliveries(): HasMany {
        return $this->hasMany(CustomerDelivery::class);
    }

    public function next_customer_deliveries(): HasMany {
        return $this->customer_deliveries()->whereHas('delivery', function ($query) {
            $query->where('date', '<=', now()->addDays(CustomerDelivery::PREVIEW_OFFSET))->where('date', '>=', now());
        });
    }
}
