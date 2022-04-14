<?php

namespace App\Rules;

use App\Models\DeliveryService;
use Illuminate\Contracts\Validation\Rule;

class DeliveryPossibleToPostcode implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !is_null(DeliveryService::findServiceForPostcode($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Es werden keine Lieferungen an die PLZ angeboten.';
    }
}
