<?php

namespace App\Exceptions;

use App\Models\Delivery;
use Exception;

class DeliveryException extends Exception
{
    public static function deadlineHasPassed(Delivery $delivery): DeliveryException
    {
        return new static("Die Bestellung lässt sich nicht mehr bearbeiten, da die Abmeldefrist am ".$delivery->deadline->format('d.m.Y')." abgelaufen ist.");
    }
}
