<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function bundleImg(Bundle $bundle): BinaryFileResponse
    {
        if(!is_null($bundle->img_path)) {
            $file = storage_path('app/media/img/'.$bundle->img_path);
        } else {
            $file = public_path('img/product_default.jpg');
        }

        if(!file_exists($file)) {
            $file = public_path('img/product_default.jpg');
        }

        Log::info($file);

        return response()->file($file);
    }
}
