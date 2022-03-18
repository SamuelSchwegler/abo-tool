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

    public function getPriceAttribute($db)
    {
        return ! is_null($db) ? number_format($db / 100, 2, '.', '\'') : null;
    }
}
