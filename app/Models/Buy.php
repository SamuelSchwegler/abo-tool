<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buy extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bundle()
    {
        return $this->belongsTo(Bundle::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
