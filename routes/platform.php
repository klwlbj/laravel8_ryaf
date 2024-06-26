<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Platform\AuthController;
use App\Http\Controllers\Platform\ReportController;
use App\Http\Controllers\Platform\ChargeCellController;
use App\Http\Controllers\Platform\CallbackUrlController;
use App\Http\Controllers\Platform\ChargeStationController;
use App\Http\Controllers\Platform\ChargingRecordController;
use App\Http\Controllers\Platform\ChargeEquipmentController;

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
    Route::post('/report/chargingMetaInfo', [ReportController::class, 'chargingMetaInfo']);
    Route::post('/report/chargingMetaInfoList', [ReportController::class, 'chargingMetaInfoList']);

    Route::post('/management/chargingRecord', [ChargingRecordController::class, 'chargingRecordSubmit']);
    Route::get('/management/chargingRecord', [ChargingRecordController::class, 'chargingRecordList']);
    Route::post('/management/chargingRecordList', [ChargingRecordController::class, 'chargingRecordListSubmit']);

    Route::put('management/callbackUrl/reportFailure', [CallbackUrlController::class, 'updateReportFailure']);
    Route::delete('management/callbackUrl/reportFailure', [CallbackUrlController::class, 'deleteReportFailure']);

    Route::put('management/callbackUrl/getChargingMetaInfo', [CallbackUrlController::class, 'updateGetChargingMetaInfo']);
    Route::delete('management/callbackUrl/getChargingMetaInfo', [CallbackUrlController::class, 'deleteGetChargingMetaInfo']);

    Route::put('management/callbackUrl/notifyCellRisk', [CallbackUrlController::class, 'updateNotifyCellRisk']);

    Route::delete('/management/station', [ChargeStationController::class, 'destroy']);
    Route::post('/management/station', [ChargeStationController::class, 'store']);
    Route::put('/management/station', [ChargeStationController::class, 'update']);
    Route::get('/management/station', [ChargeStationController::class, 'index']);

    Route::delete('/management/equipment', [ChargeEquipmentController::class, 'destroy']);
    Route::post('/management/equipment', [ChargeEquipmentController::class, 'store']);
    Route::put('/management/equipment', [ChargeEquipmentController::class, 'update']);
    Route::get('/management/equipment', [ChargeEquipmentController::class, 'index']);

    Route::delete('/management/cell', [ChargeCellController::class, 'destroy']);
    Route::post('/management/cell', [ChargeCellController::class, 'store']);
    Route::put('/management/cell', [ChargeCellController::class, 'update']);
    Route::get('/management/cell', [ChargeCellController::class, 'index']);
});
