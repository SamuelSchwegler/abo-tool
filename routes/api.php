<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\BundleController;
use App\Http\Controllers\api\BuyController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\DeliveryController;
use App\Http\Controllers\api\DeliveryServiceController;
use App\Http\Controllers\api\ItemController;
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

Route::get('/postcode-delivery-service', [DeliveryServiceController::class, 'postcodeDeliveryService']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('orders', [OrderController::class, 'orders']);
    Route::group(['middleware' => ['permission:manage customers']], function () {
        Route::get('orders/{customer}', [OrderController::class, 'orders']);
    });

    Route::patch('/order/{order}', [OrderController::class, 'update']);
    Route::patch('/order/{order}/toggle-cancel', [OrderController::class, 'toggleCancel']);

    Route::get('buy/{buy}', [BuyController::class, 'buy']);
    Route::patch('/buy/{buy}', [BuyController::class, 'update']);

    Route::post('buy', [BuyController::class, 'issue']);

    Route::group(['middleware' => ['permission:manage customers']], function () {
        Route::get('payments', [BuyController::class, 'payments']);
        Route::get('buys/{customer}', [BuyController::class, 'customer']);
        Route::delete('buy/{buy}', [BuyController::class, 'delete']);
    });

    Route::get('delivery-services', [DeliveryServiceController::class, 'services']);
    Route::patch('/delivery-service/{service}/', [DeliveryServiceController::class, 'update']);
    Route::post('/delivery-service/', [DeliveryServiceController::class, 'store']);

    Route::post('/delivery-service/{service}/add/', [DeliveryServiceController::class, 'apiAddPostcode']);
    Route::post('/delivery-service/{service}/remove/', [DeliveryServiceController::class, 'apiRemovePostcode']);

    Route::get('/deliveries', [DeliveryController::class, 'deliveries']);
    Route::get('/deliveries/{date}', [DeliveryController::class, 'deliveriesForDate']);

    Route::get('/delivery/{delivery}', [DeliveryController::class, 'delivery']);
    Route::patch('/delivery/{delivery}/toggle-approved', [DeliveryController::class, 'toggleApproved']);

    Route::patch('/delivery/{delivery}', [DeliveryController::class, 'update']);

    // delivery items
    Route::post('/delivery/{delivery}/{product}/item', [DeliveryController::class, 'addItem']);
    Route::delete('/delivery/{delivery}/{product}/item/{item}', [DeliveryController::class, 'removeItem']);

    Route::get('/items', [ItemController::class, 'items']);

    Route::get('/customers', [CustomerController::class, 'customers']);
    Route::get('/customer/{customer}', [CustomerController::class, 'customer']);
    Route::patch('/customer/{customer}', [CustomerController::class, 'update']);
    Route::post('/customer', [CustomerController::class, 'store']);

    Route::patch('/customer/{customer}/used-orders', [CustomerController::class, 'updateUsedOrders']);
});
