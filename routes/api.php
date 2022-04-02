<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BundleController;
use App\Http\Controllers\api\BuyController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\DeliveryController;
use App\Http\Controllers\api\DeliveryServiceController;
use App\Http\Controllers\api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('guest')->name('password.email');
Route::post('reset-password', [AuthController::class, 'submitResetPassword'])->middleware('guest')->name('password.reset');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('bundles', [BundleController::class, 'bundles']);
Route::get('bundle/{bundle}', [BundleController::class, 'bundle']);

Route::post('bundle/{bundle}/buy', [BundleController::class, 'submitBuy']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('orders', [OrderController::class, 'orders']);
    Route::group(['middleware' => ['permission:manage customers']], function () {
        Route::get('orders/{customer}', [OrderController::class, 'orders']);
    });

    Route::patch('/order/{order}', [OrderController::class, 'update']);
    Route::patch('/order/{order}/toggle-cancel', [OrderController::class, 'toggleCancel']);

    Route::get('payments', [BuyController::class, 'payments']);
    Route::get('buy/{buy}', [BuyController::class, 'buy']);
    Route::patch('/buy/{buy}', [BuyController::class, 'update']);

    Route::get('delivery-services', [DeliveryServiceController::class, 'services']);
    Route::patch('/delivery-service/{service}/', [DeliveryServiceController::class, 'update']);
    Route::post('/delivery-service/', [DeliveryServiceController::class, 'store']);

    Route::post('/delivery-service/{service}/add/', [DeliveryServiceController::class, 'apiAddPostcode']);
    Route::post('/delivery-service/{service}/remove/', [DeliveryServiceController::class, 'apiRemovePostcode']);

    Route::get('/deliveries', [DeliveryController::class, 'deliveries']);
    Route::get('/delivery/{delivery}', [DeliveryController::class, 'delivery']);
    Route::patch('/delivery/{delivery}/toggle-approved', [DeliveryController::class, 'toggleApproved']);

    Route::get('/customers', [CustomerController::class, 'customers']);
    Route::get('/customer/{customer}', [CustomerController::class, 'customer']);
});
