<?php

namespace App\Exceptions;

use App\Models\Order;
use Carbon\Carbon;
use Exception;

class OrderEditException extends Exception
{
    public static function deadlineHasPassed(Order $order): OrderEditException
    {
        $delivery = $order->delivery;
        return new static("Die Bestellung lässt sich nicht mehr bearbeiten, da die Abmeldefrist am ".$delivery->deadline->format('d.m.Y')." abgelaufen ist.");
    }
}
