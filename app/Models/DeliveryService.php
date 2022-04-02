<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryService extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'days' => 'array'
    ];

    public function postcodes(): HasMany
    {
        return $this->hasMany(Postcode::class)->orderBy('postcode');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class)->orderBy('deadline');
    }

    public static function rules(): array
    {
        return [
            'name' => ['required'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['required', 'string'],
            'deadline_distance' => ['required', 'min:0', 'max:14', 'numeric']
        ];
    }
}
