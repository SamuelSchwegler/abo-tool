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

        return response([
            'bundles' => BundleResource::collection(Bundle::orderBy('order')->get()),
            'texts' => [
                'home_title' => $setting->texts['home_title'] ?? 'Seitentitel',
                'home_description' => $setting->texts['home_description'] ?? '',
            ],
        ]);
    }
}
