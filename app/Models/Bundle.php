<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bundle extends Model
{
    protected $guarded = ['id'];

    public function getPriceAttribute($db)
    {
        return !is_null($db) ? number_format($db / 100, 2, '.','\'') : null;
    }
}
