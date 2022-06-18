<?php

namespace App\Http\Controllers;

use App\Exports\DeliveryAddresses;
use App\Models\Delivery;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DeliveryController extends Controller
{
    public function exportDeliveryNotes(Delivery $delivery): BinaryFileResponse
    {
        return response()->download($delivery->exportDeliveryNotes());
    }

    public function exportDeliveryAddresses(Delivery $delivery): BinaryFileResponse
    {
        $name = 'adressen_'.$delivery->delivery_service->name.'_'.$delivery->date->format('Y-m-d').'.xlsx';

        return Excel::download(new DeliveryAddresses($delivery), $name);
    }
}
