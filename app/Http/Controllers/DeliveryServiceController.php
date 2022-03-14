<?php

namespace App\Http\Controllers;

use App\Http\Resources\DeliveryServiceResource;
use App\Models\DeliveryService;
use App\Models\Postcode;
use Illuminate\Http\Request;

class DeliveryServiceController extends Controller
{
    public function create() {
        return view('delivery-service.create')->with([
            'services' => DeliveryService::all(),
        ]);
    }

    public function store(Request $request) {
        $validated = $request->validate(DeliveryService::rules());

        $service = DeliveryService::create($validated);

        return redirect(route('delivery-service.edit', $service));
    }

    public function edit(?DeliveryService $service = null)
    {
        if (is_null($service)) {
            $service = DeliveryService::first();
        }

        return view('delivery-service.edit')->with([
            'service' => $service,
            'services' => DeliveryService::all(),
            'serviceResource' => new DeliveryServiceResource($service),
        ]);
    }

    public function apiUpdate(DeliveryService $service, Request $request)
    {
        $validated = $request->validate(DeliveryService::rules());

        $service->update([
            'name' => $validated['name'],
        ]);

        return response([
            'message' => 'ok',
            'service' => new DeliveryServiceResource($service),
        ], 200);
    }

    public function apiAddPostcode(DeliveryService $service, Request $request)
    {
        $validated = $request->validate([
            'postcode' => ['required'],
        ]);

        Postcode::updateOrCreate([
            'postcode' => $validated['postcode'],
        ], [
            'delivery_service_id' => $service->id,
        ]);

        $service->refresh();

        return response([
            'message' => 'ok',
            'service' => new DeliveryServiceResource($service),
        ], 200);
    }

    public function apiRemovePostcode(DeliveryService $service, Request $request)
    {
        $validated = $request->validate([
            'postcode' => ['required'],
        ]);

        Postcode::where('postcode', $validated['postcode'])->delete();

        $service->refresh();

        return response([
            'message' => 'ok',
            'service' => new DeliveryServiceResource($service),
        ], 200);
    }
}
