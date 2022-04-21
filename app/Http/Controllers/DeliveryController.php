<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DeliveryController extends Controller
{
    public function exportDeliveryNotes(Delivery $delivery): BinaryFileResponse
    {
        return response()->download($delivery->exportDeliveryNotes());
    }
}
