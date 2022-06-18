<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DeliveryProductItem extends Pivot
{
    protected $table = "delivery_product_items";
    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
