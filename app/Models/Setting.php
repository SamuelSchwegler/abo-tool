<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'texts' => 'array',
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
