<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuyResource;
use App\Jobs\CreateOrdersForBuy;
use App\Models\Buy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\assertNotNull;
use function response;

class BuyController extends Controller
{
    public function buy(Buy $buy) {
        return response(['buy' => BuyResource::make($buy)]);
    }

    public function update(Buy $buy, Request $request) {
        assertNotNull($buy);

        $validated = $request->validate([
            'paid' => ['nullable', 'boolean']
        ]);
        $buy->update($validated);

        if($request->has('paid') && $buy->paid) {
            Log::info('before job');
            CreateOrdersForBuy::dispatch($buy);
        }

        return response(['buy' => BuyResource::make($buy)]);
    }

    public function payments() {
        $buys = Buy::orderBy('issued')->where(function($query) {
            $query->where('paid', 0)->orWhere('issued', '>=', now()->subWeeks(2));
        })->get();

        return response([
            'buys' => BuyResource::collection($buys)
        ]);
    }
}
