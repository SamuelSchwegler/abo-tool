<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryService extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'days' => 'array',
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
            'deadline_distance' => ['required', 'min:0', 'max:14', 'numeric'],
            'delivery_cost' => ['required', 'min:0', 'max:9999', 'numeric'],
            'pickup' => ['nullable', 'boolean'],
            'option_description' => ['nullable', 'string'],
        ];
    }

    public static function messages(): array
    {
        return [
            'days.required' => __('Please enter at least one delivery day.'),
        ];
    }

    public function customers()
    {
        if (! $this->pickup) {
            return Customer::leftJoin('addresses', 'addresses.id', 'customers.delivery_address_id')
                ->leftJoin('postcodes', 'postcodes.postcode', 'addresses.postcode')
                ->leftJoin('delivery_services', 'delivery_services.id', 'postcodes.delivery_service_id')
                ->where('delivery_services.id', $this->id)
                ->select('customers.*')->get();
        } else {
            return Customer::whereNull('delivery_address_id')->where('delivery_service_id', $this->id)->get();
        }

    }

    /**
     * @return Attribute
     */
    protected function deliveryCost(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }

    /**
     * Gibt den passenden Lieferdienst für eine Postleitzahl zurück.
     *
     * @param  string  $postcode
     * @return ?DeliveryService
     */
    public static function findServiceForPostcode(string $postcode): ?DeliveryService
    {
        return self::whereHas('postcodes', function ($query) use ($postcode) {
            $query->where('postcode', $postcode);
        })->first();
    }
}
