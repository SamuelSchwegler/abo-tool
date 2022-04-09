<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryService extends Model
{
    use HasFactory;

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

    public function customers() {
        return Customer::leftJoin('addresses', 'addresses.id', 'customers.delivery_address_id')
            ->leftJoin('postcodes', 'postcodes.postcode', 'addresses.postcode')
            ->leftJoin('delivery_services', 'delivery_services.id', 'postcodes.delivery_service_id')
            ->where('delivery_services.id', $this->id)
            ->select('customers.*')->get();
    }
}
