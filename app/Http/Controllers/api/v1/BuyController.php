<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuyResource;
use App\Models\Buy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\assertNotNull;

class BuyController extends Controller
{
    public function update(Buy $buy, Request $request) {
        assertNotNull($buy);

        $validated = $request->validate([
            'paid' => ['nullable', 'boolean']
        ]);
        $buy->update($validated);
        Log::info($validated);

        return response(['buy' => BuyResource::make($buy)]);
    }
}
