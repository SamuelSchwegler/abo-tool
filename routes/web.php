<?php

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

Route::get('order/contact', [OrderController::class, 'contact'])->name('order.contact');

Route::middleware([
    'auth',
])->group(function () {
    Route::get('deliveries/customer', [DeliveryController::class, 'customer'])->name('deliveries.customer');
});
require __DIR__.'/auth.php';
