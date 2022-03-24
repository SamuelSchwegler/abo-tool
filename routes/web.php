<?php

use App\Http\Controllers\BuyController;
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

Route::middleware('auth')->group(function() {
    Route::get('export/buy/{buy}/bill', [BuyController::class, 'exportBill'])->name('buy.export.bill');
});

Route::get('/{any}', function (){
    return view('app');
})->where('any', '^((?!export).)*$');

/*
Route::prefix('buy')->group(function () {
    Route::get('{bundle}/contact', [BuyController::class, 'contact'])->name('buy.contact');

    Route::post('{bundle}/registration', [BuyController::class, 'registrationSubmit'])->name('buy.registration');

    Route::get('{buy}/payment', [BuyController::class, 'payment'])->name('buy.payment');
    Route::post('{buy}/payment', [BuyController::class, 'paymentSubmit']);
    Route::get('{buy}/confirmation', [BuyController::class, 'confirmation'])->name('buy.confirmation');

});

Route::middleware([
    'auth',
])->group(function () {
    Route::get('orders/customer', [OrderController::class, 'customer'])->name('orders.customer');
    Route::get('order/{order}/download', [OrderController::class, 'exportDeliveryNote']);
});
require __DIR__ . '/auth.php';
*/
