<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NBController;
use App\Http\Controllers\CTWingController;
use App\Http\Controllers\OneNetController;
use App\Http\Controllers\WanLinYunController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 万霖云
Route::post('/wly/common', [WanLinYunController::class, 'common']);
Route::post('/heartbeat', [WanLinYunController::class, 'heartbeat']);
Route::post('/event', [WanLinYunController::class, 'event']);
Route::post('/offline', [WanLinYunController::class, 'offline']);
Route::post('/iccid', [WanLinYunController::class, 'iccid']);

Route::post('/wly/remoteControl/{chipcode}/{clientId}/{runTime}/{switchState}', [WanLinYunController::class, 'remoteControl']);

// 移动onenet
Route::get('/getSign', [OneNetController::class, 'echoSign']);
Route::get('/loadResource', [OneNetController::class, 'loadResource']);
Route::get('/cacheCommands', [OneNetController::class, 'cacheCommands']);
Route::get('/cacheCommand/{uuid}', [OneNetController::class, 'cacheCommand']);
Route::get('/cancelCacheCommand/{uuid}', [OneNetController::class, 'cancelCacheCommand']);
Route::get('/cancelAllCacheCommand', [OneNetController::class, 'cancelAllCacheCommand']);
Route::get('/issueCacheCommand/{args}', [OneNetController::class, 'issueCacheCommand']);
Route::get('/writeResource/{args}/{dwPackageNo}', [OneNetController::class, 'writeResource']);
Route::get('/logQuery/{uuid}', [OneNetController::class, 'logQuery']);

// 电信ctwing
// 事件上报
Route::get('/ctwing/queryDeviceEventList/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'queryDeviceEventList']);
Route::get('/ctwing/queryDeviceEventTotal/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'queryDeviceEventTotal']);
// http消息订阅
Route::get('/ctwing/getSubscriptionsList/{productId}/{masterKey}/{pageNow}/{pageSize}', [CTWingController::class, 'getSubscriptionsList']);
Route::get('/ctwing/getSubscription/{productId}/{masterKey}/{subId}', [CTWingController::class, 'getSubscription']);
Route::get('/ctwing/deleteSubscription/{productId}/{subId}/{masterKey}/{subLevel}', [CTWingController::class, 'deleteSubscription']);
Route::get('/ctwing/createSubscription/{productId}/{deviceId}/{masterKey}/{subUrl}/{subLevel}', [CTWingController::class, 'createSubscription']);
// 指令下发
Route::get('/ctwing/queryCommandList/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'queryCommandList']);
Route::get('/ctwing/queryCommand/{productId}/{deviceId}/{masterKey}/{commandId}', [CTWingController::class, 'queryCommand']);
Route::get('/ctwing/cancelCommand/{productId}/{deviceId}/{masterKey}/{commandId}', [CTWingController::class, 'cancelCommand']);
Route::get('/ctwing/cancelAllCommand/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'cancelAllCommand']);
Route::get('/ctwing/createCommandLwm2mProfile/{productId}/{deviceId}/{masterKey}/{command}/{dwPackageNo}', [CTWingController::class, 'createCommandLwm2mProfile']);
Route::get('/ctwing/createCommand/{productId}/{deviceId}/{masterKey}/{command}/{dwPackageNo}', [CTWingController::class, 'createCommand']);

// http回调
Route::get('/nbWarm', [NBController::class, 'nbWarm']);
Route::get('/hkWarm', [NBController::class, 'nbWarm']);
Route::post('/nbWarm', [NBController::class, 'nbReceived']);
Route::post('/hkWarm', [NBController::class, 'hkReceived']);
Route::post('/hkCTWingWarm', [NBController::class, 'hkCTWingWarm']);
Route::post('/hkCTWing4GWarm', [NBController::class, 'hkCTWing4GWarm']);

// 海康指令解析测试
Route::get('/analyze', [NBController::class, 'analyze']);
