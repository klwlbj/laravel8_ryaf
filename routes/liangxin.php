<?php

use App\Http\Controllers\DaHua\DevicesController;
use App\Http\Controllers\DaHua\ReportController;
use App\Http\Controllers\LiangXinController;
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
Route::group(['middleware' => ['liangXinCheckSign']], function () {
    Route::prefix('bfm/open')->group(function () {
        Route::prefix('query')->group(function () {
            Route::any('/town', [LiangXinController::class, 'getTown']);
            Route::any('/rust', [LiangXinController::class, 'getRust']);
        });

        Route::prefix('register')->group(function () {
            Route::any('/unit', [LiangXinController::class, 'addUnit']);
            Route::any('/device', [LiangXinController::class, 'addDevice']);
        });

        Route::prefix('update')->group(function () {
            Route::any('/unit', [LiangXinController::class, 'updateUnit']);
            Route::any('/device', [LiangXinController::class, 'updateDevice']);
        });

        Route::prefix('unregister')->group(function () {
            Route::any('/unit', [LiangXinController::class, 'unregisterUnit']);
            Route::any('/device', [LiangXinController::class, 'unregisterDevice']);
        });
    });
});

Route::prefix('blkk')->group(function () {
    Route::any('/notify', [LiangXinController::class, 'notify']);
});

