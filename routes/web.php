<?php

use App\Http\Controllers\api\v1\BuyController as ApiBuyController;
use App\Http\Controllers\api\v1\OrderController as ApiOrderController;
use App\Http\Controllers\BuyController;
use App\Http\Controllers\DeliveryServiceController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function (){
    return view('app');
})->name('home');
Route::get('/{any}', function (){
    return view('app');
})->where('any', '.*');;
/*
Route::prefix('buy')->group(function () {
    Route::get('{bundle}/contact', [BuyController::class, 'contact'])->name('buy.contact');

    Route::post('{bundle}/registration', [BuyController::class, 'registrationSubmit'])->name('buy.registration');

    Route::get('{buy}/payment', [BuyController::class, 'payment'])->name('buy.payment');
    Route::post('{buy}/payment', [BuyController::class, 'paymentSubmit']);
    Route::get('{buy}/confirmation', [BuyController::class, 'confirmation'])->name('buy.confirmation');

    Route::get('{buy}/bill', [BuyController::class, 'exportBill'])->name('buy.export.bill');
});

Route::middleware([
    'auth',
])->group(function () {
    Route::get('orders/customer', [OrderController::class, 'customer'])->name('orders.customer');
    Route::get('order/{order}/download', [OrderController::class, 'exportDeliveryNote']);

    Route::get('delivery-service', [DeliveryServiceController::class, 'create'])->name('delivery-service.create');
    Route::post('delivery-service', [DeliveryServiceController::class, 'store']);

    Route::get('delivery-service/{service}', [DeliveryServiceController::class, 'edit'])->name('delivery-service.edit');
    Route::get('delivery-service-default/', [DeliveryServiceController::class, 'edit'])->name('delivery-service.edit-default');

    Route::get('manage-payments', [BuyController::class, 'managePayments'])->name('buy.payments');

    Route::prefix('api/v1')->group(function () {
        Route::patch('/delivery-service/{service}/', [DeliveryServiceController::class, 'apiUpdate']);
        Route::post('/delivery-service/{service}/add/', [DeliveryServiceController::class, 'apiAddPostcode']);
        Route::post('/delivery-service/{service}/remove/', [DeliveryServiceController::class, 'apiRemovePostcode']);

        Route::patch('/order/{order}', [ApiOrderController::class, 'update']);
        Route::patch('/order/{order}/toggle-cancel', [ApiOrderController::class, 'toggleCancel']);

        Route::patch('/buy/{buy}', [ApiBuyController::class, 'update']);
    });
});
require __DIR__ . '/auth.php';
*/
