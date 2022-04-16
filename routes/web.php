<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\BuyController;
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

Route::get('/reset-password/{token}', function ($token) {
    return view('app');
})->middleware('guest')->name('password.reset');

Route::middleware('auth')->group(function() {
    Route::get('export/buy/{buy}/bill', [BuyController::class, 'exportBill'])->name('buy.export.bill');
    Route::get('export/delivery-note/{order}', [OrderController::class, 'exportDeliveryNote'])->name('delivery-note.export');
});

Route::get('/{any}', function (){
    return view('app');
})->where('any', '^((?!export).)*$');
