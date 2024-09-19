<?php

use App\Http\Controllers\DaHua\DevicesController;
use App\Http\Controllers\DaHua\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DaHua\UnitsController;

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
Route::group(['middleware' => ['checkIp']], function () {
    Route::any('/units/getList', [UnitsController::class, 'getList']);
    Route::any('/devices/getList', [DevicesController::class, 'getList']);
});

Route::any('/report', [ReportController::class, 'report']);

