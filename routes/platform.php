<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Platform\AuthController;
use App\Http\Controllers\Platform\ChargeStationController;

/*
|--------------------------------------------------------------------------
| Platform Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//不需要token的接口
Route::group(['middleware' => ['platformCheckSign']], function () {
    Route::get('/management/operatorAPIToken', [AuthController::class, 'operatorAPIToken']);
});
//需要token的接口
Route::group(['middleware' => ['platformCheckToken', 'platformCheckSign']], function () {
    Route::delete('/management/station', [ChargeStationController::class, 'destroy']);
    Route::post('/management/station', [ChargeStationController::class, 'store']);
    Route::put('/management/station', [ChargeStationController::class, 'update']);
    Route::get('/management/station', [ChargeStationController::class, 'index']);
});
