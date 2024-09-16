<?php

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

Route::group(['middleware' => ['admin', 'auth']], function() {
    Route::post('workorder/approve', [\App\Http\Controllers\WorkorderController::class, 'approve'])
        ->name('workorder.approve');
    Route::resource('workorder', \App\Http\Controllers\WorkorderController::class);

    Route::resource('montir', \App\Http\Controllers\MontirController::class);
    Route::resource('customer', \App\Http\Controllers\CustomerController::class);
    Route::resource('barang', \App\Http\Controllers\BarangController::class);

    Route::get('jualnota/{nomor}/pracetak', [\App\Http\Controllers\JualNotaController::class, 'pracetak'])
        ->name('jualnota.pracetak');
    Route::resource('jualnota', \App\Http\Controllers\JualNotaController::class);

    Route::get('/', function () {
        return view('welcome');
    });
});
