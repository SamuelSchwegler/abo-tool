<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function rules(string $prefix = ''): array
    {
        $rules = [
            $prefix.(strlen($prefix) > 0 ? '.' : '').'street' => ['required', 'string'],
            $prefix.(strlen($prefix) > 0 ? '.' : '').'postcode' => ['required', 'string'],
            $prefix.(strlen($prefix) > 0 ? '.' : '').'city' => ['required', 'string'],
        ];

        if (strlen($prefix) > 0) {
            $rules[$prefix] = ['required', 'array'];
        }

        return $rules;
    }

    public static function messages(string $prefix = ''): array
    {
        $messages = [
            $prefix.(strlen($prefix) > 0 ? '.' : '').'street.required' => 'Strasse muss ausgefüllt sein',
            $prefix.(strlen($prefix) > 0 ? '.' : '').'postcode.required' => 'Postleitzahl muss ausgefüllt sein',
            $prefix.(strlen($prefix) > 0 ? '.' : '').'city.required' => 'Ort muss ausgefüllt sein',
        ];

        return $messages;
    }
}
