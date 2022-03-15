<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    const PREVIEW_OFFSET = 10; // wie viele Lieferungen voraus werden Orders angezeigt.

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deadlinePassed(): bool
    {
        return $this->delivery->deadline->lt(now());
    }
}
