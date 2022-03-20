<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bundle extends Model
{
    protected $guarded = ['id'];

    public function buys(): HasMany {
        return $this->hasMany(Buy::class);
    }

    public function getFormatedPriceAttribute(): string
    {
        return number_format($this->price / 100, 2, '.', '\'');
    }
}
