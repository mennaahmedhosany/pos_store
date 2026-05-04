<?php

use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/',                [PosController::class, 'index'])->name('pos.index');
Route::post('/calculate-item', [PosController::class, 'calculateItem'])->name('pos.calculateItem');
Route::post('/orders',         [PosController::class, 'store'])->name('pos.store');
Route::get('/orders',          [PosController::class, 'orders'])->name('pos.orders');
Route::get('/orders/{order}',  [PosController::class, 'show'])->name('pos.show');
