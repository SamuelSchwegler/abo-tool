<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function settings() {
        $settings = Setting::first();

        return response([
            'settings' => $settings
        ]);
    }

    public function updateTexts(Request $request) {

        $rules = [
            'home_title' => ['required', 'string'],
            'home_description' => ['required', 'string']
        ];

        $validated = $request->validate($rules);

        $setting = Setting::first();
        $texts = $setting->texts;

        foreach(array_keys($rules) as $key) {
            $texts[$key] = $validated[$key];
        }

        $setting->update([
            'texts' => $texts
        ]);
    }
}
