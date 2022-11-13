<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryServiceResource;
use App\Jobs\CreateDeliveries;
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
            'services' => DeliveryServiceResource::collection(DeliveryService::all()),
        ]);
    }

    public function store(Request $request): Response
    {
        $validated = $request->validate(DeliveryService::rules());

        $service = DeliveryService::create($validated);
        CreateDeliveries::dispatchSync($service);

        return \response([
            'service' => DeliveryServiceResource::make($service),
        ]);
    }

    public function update(DeliveryService $service, Request $request): Response|Application|ResponseFactory
    {
        $validated = $request->validate(DeliveryService::rules());

        $service->update($validated);

        return response([
            'message' => 'ok',
            'service' => new DeliveryServiceResource($service),
        ], 200);
    }

    public function apiAddPostcode(DeliveryService $service, Request $request): Response|Application|ResponseFactory
    {
        $validated = $request->validate([
            'postcode' => ['required'],
        ]);

        $postcode = Postcode::updateOrCreate([
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

    public function apiRemovePostcode(DeliveryService $service, Request $request): Response|Application|ResponseFactory
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

    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory
     * @changes v0.1.3 - strict modus
     */
    public function postcodeDeliveryService(Request $request): Response|Application|ResponseFactory
    {
        $validated = $request->validate([
            'postcode' => ['required'],
            'strict' => ['nullable', 'boolean']
        ]);

        $strict = $request->strict ?? false;
        $service = DeliveryService::findServiceForPostcode($validated['postcode']);

        if(!$strict && is_null($service))
            $service = DeliveryService::where('pickup', 1)->first();

        return \response([
            'msg' => 'ok',
            'service' => !is_null($service) ? (new DeliveryServiceResource($service)) : ['id' => 0, 'name' => 'keine Lieferung'],
        ]);
    }
}
