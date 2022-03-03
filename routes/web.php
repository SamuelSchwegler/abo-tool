<?php

use App\Http\Controllers\BuyController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::prefix('buy')->group(function () {
    Route::get('{bundle}/contact', [BuyController::class, 'contact'])->name('buy.contact');
    Route::get('{buy}/payment', [BuyController::class, 'payment'])->name('buy.payment');
    Route::post('{buy}/payment', [BuyController::class, 'paymentSubmit']);
    Route::get('{buy}/confirmation', [BuyController::class, 'confirmation'])->name('buy.confirmation');

    Route::get('manage-payments', [BuyController::class, 'managePayments'])->name('buy.payments');
});

Route::middleware([
    'auth',
])->group(function () {
    Route::get('orders/customer', [OrderController::class, 'customer'])->name('orders.customer');
});
require __DIR__.'/auth.php';
