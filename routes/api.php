<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'browse'], function() {
    Route::get('montir', [\App\Http\Controllers\Api\BrowseController::class, 'montir']);
    Route::get('customer', [\App\Http\Controllers\Api\BrowseController::class, 'customer']);
    Route::get('barang', [\App\Http\Controllers\Api\BrowseController::class, 'barang']);
    Route::get('gudang', [\App\Http\Controllers\Api\BrowseController::class, 'gudang']);
});
