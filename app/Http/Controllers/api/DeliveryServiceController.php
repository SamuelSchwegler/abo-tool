<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryServiceResource;
use App\Models\DeliveryService;
use App\Models\Postcode;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeliveryServiceController extends Controller
{
    public function services(): Response|Application|ResponseFactory
    {
        return response([
            'services' => DeliveryServiceResource::collection(DeliveryService::all())
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(DeliveryService::rules());

        $service = DeliveryService::create($validated);

        return \response([
            'service' => DeliveryServiceResource::make($service)
        ]);
    }

    public function update(DeliveryService $service, Request $request)
    {
        $validated = $request->validate(DeliveryService::rules());

        $service->update($validated);

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

    public function postcodeDeliveryService(Request $request): Response|Application|ResponseFactory
    {
        $validated = $request->validate([
            'postcode' => ['required']
        ]);

        $service = DeliveryService::findServiceForPostcode($validated['postcode']) ?? DeliveryService::where('pickup', 1)->first();

        return \response([
            'msg' => 'ok',
            'service' => new DeliveryServiceResource($service)
        ]);
    }
}
