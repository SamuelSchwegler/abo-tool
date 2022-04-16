<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function item_origin() {
        return $this->belongsTo(ItemOrigin::class);
    }

    public function deliveries() {
        return $this->belongsToMany(Delivery::class);
    }
}
