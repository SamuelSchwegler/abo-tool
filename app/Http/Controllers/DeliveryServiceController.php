<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryServiceResource;
use App\Models\DeliveryService;
use App\Models\Postcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryServiceController extends Controller
{
    public function edit(?DeliveryService $service = null)
    {
        if (is_null($service)) {
            $service = DeliveryService::first();
        }

        return view('delivery-service.edit')->with([
            'service' => $service,
            'services' => DeliveryService::all(),
            'serviceResource' => new DeliveryServiceResource($service)
        ]);
    }

    public function apiUpdate(DeliveryService $service, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required']
        ]);

        $service->update([
            'name' => $validated['name']
        ]);

        return response([
            'message' => 'ok',
            'service' => new DeliveryServiceResource($service)
        ], 200);
    }

    public function apiAddPostcode(DeliveryService $service, Request $request)
    {
        $validated = $request->validate([
            'postcode' => ['required']
        ]);

        Postcode::updateOrCreate([
            'postcode' => $validated['postcode']
        ], [
            'delivery_service_id' => $service->id
        ]);

        $service->refresh();

        return response([
            'message' => 'ok',
            'service' => new DeliveryServiceResource($service)
        ], 200);
    }
}
