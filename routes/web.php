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

Route::get('buy/contact', [BuyController::class, 'contact'])->name('buy.contact');

Route::middleware([
    'auth',
])->group(function () {
    Route::get('orders/customer', [OrderController::class, 'customer'])->name('orders.customer');
});
require __DIR__.'/auth.php';
