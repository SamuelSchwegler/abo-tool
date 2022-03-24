<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BundleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\HomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('bundles', [BundleController::class, 'bundles']);
Route::get('bundle/{bundle}', [BundleController::class, 'bundle']);

Route::post('bundle/{bundle}/buy', [BundleController::class, 'submitBuy']);

Route::middleware('auth:sanctum')->group(function() {
});
