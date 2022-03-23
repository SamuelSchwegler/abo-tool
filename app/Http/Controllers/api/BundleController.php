<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Models\Bundle;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    public function bundles()
    {
        return BundleResource::collection(Bundle::all());
    }

    public function bundle(Bundle $bundle)
    {
        return BundleResource::make($bundle);
    }
}
