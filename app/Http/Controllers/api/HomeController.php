<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Models\Bundle;
use App\Models\Setting;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function getHomeData(): Response
    {
        $setting = Setting::first();

        $bundles = Bundle::orderBy('order')->when(now()->gt('2023-10-13 23:59:00'), function ($q) {
            $q->where('trial', '!=', 0);
        })->when(now()->gt('2023-11-10 23:59:00'), function ($q) {
            $q->where('trial', '!=', 1);
        })->get();

        return response([
            'bundles' => BundleResource::collection($bundles),
            'texts' => [
                'home_title' => $setting->texts['home_title'] ?? 'Seitentitel',
                'home_description' => $setting->texts['home_description'] ?? '',
            ],
        ]);
    }
}
