<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NBController;
use App\Http\Controllers\DaHuaController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\CTWingController;
use App\Http\Controllers\LiuRuiController;
use App\Http\Controllers\OneNetController;
use App\Http\Controllers\WanLinYunController;
use App\Http\Controllers\HikvisionCloudController;

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
Route::prefix('onenet')->group(function () {
    Route::get('/getSign', [OneNetController::class, 'echoSign']);
    Route::get('/loadResource/{imei}', [OneNetController::class, 'loadResource']);
    Route::get('/cacheCommands/{imei}', [OneNetController::class, 'cacheCommands']);
    Route::get('/cacheCommand/{imei}/{uuid}', [OneNetController::class, 'cacheCommand']);
    Route::get('/cancelCacheCommand/{imei}/{uuid}', [OneNetController::class, 'cancelCacheCommand']);
    Route::get('/cancelAllCacheCommand/{imei}', [OneNetController::class, 'cancelAllCacheCommand']);
    Route::get('/issueCacheCommand/{imei}/{args}/{dwPackageNo}', [OneNetController::class, 'issueCacheCommand']);
    Route::get('/createGasSettingCommand/{imei}/{gasAlarmCorrection}', [OneNetController::class, 'createGasSettingCommand']);
    Route::get('/writeResource/{imei}/{args}/{dwPackageNo}', [OneNetController::class, 'writeResource']);
    Route::get('/execute/{imei}/{args}/{dwPackageNo}', [OneNetController::class, 'execute']);
    Route::get('/realTimewriteResource/{imei}/{args}/{dwPackageNo}', [OneNetController::class, 'realTimewriteResource']);
    Route::get('/logQuery/{imei}/{uuid}', [OneNetController::class, 'logQuery']);
});

// 电信ctwing
Route::prefix('ctwing')->group(function () {
    // 事件上报
    Route::get('/queryDeviceEventList/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'queryDeviceEventList']);
    Route::get('/queryDeviceEventTotal/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'queryDeviceEventTotal']);
    // http消息订阅
    Route::get('/getSubscriptionsList/{productId}/{masterKey}/{pageNow}/{pageSize}', [CTWingController::class, 'getSubscriptionsList']);
    Route::get('/getSubscription/{productId}/{masterKey}/{subId}', [CTWingController::class, 'getSubscription']);
    Route::get('/deleteSubscription/{productId}/{subId}/{masterKey}/{subLevel}', [CTWingController::class, 'deleteSubscription']);
    Route::get('/createSubscription/{productId}/{deviceId}/{masterKey}/{subUrl}/{subLevel}', [CTWingController::class, 'createSubscription']);
    // 指令下发
    Route::get('/queryCommandList/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'queryCommandList']);
    Route::get('/queryCommand/{productId}/{deviceId}/{masterKey}/{commandId}', [CTWingController::class, 'queryCommand']);
    Route::get('/cancelCommand/{productId}/{deviceId}/{masterKey}/{commandId}', [CTWingController::class, 'cancelCommand']);
    Route::get('/cancelAllCommand/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'cancelAllCommand']);
    Route::get('/createCommandLwm2mProfile/{productId}/{deviceId}/{masterKey}/{command}/{dwPackageNo}', [CTWingController::class, 'createCommandLwm2mProfile']);
    Route::get('/createMicrowaveSettingCommand/{productId}/{deviceId}/{masterKey}', [CTWingController::class, 'createMicrowaveSettingCommand']);
    Route::get('/createGasSettingCommand/{productId}/{deviceId}/{masterKey}/{gasAlarmCorrection}', [CTWingController::class, 'createGasSettingCommand']);
    Route::get('/createCommand/{productId}/{deviceId}/{masterKey}/{command}/{dwPackageNo}', [CTWingController::class, 'createCommand']);
    Route::get('/createNTTCommand/{productId}/{deviceId}/{masterKey}/{second}', [CTWingController::class, 'createNTTCommand']);
});

// http回调
Route::get('/nbWarm', [NBController::class, 'nbWarm']);
Route::get('/hkWarm', [NBController::class, 'nbWarm']);
Route::post('/nbWarm', [NBController::class, 'nbReceived']);
Route::post('/hkWarm', [NBController::class, 'hkReceived']);
Route::post('/hkCTWingWarm', [NBController::class, 'hkCTWingWarm']);
Route::post('/dhCTWingWarm', [NBController::class, 'dhCTWingWarm']);
Route::post('/hkCTWing4GWarm', [NBController::class, 'hkCTWing4GWarm']);

Route::any('/hmCTWing4GWarm/{string}', [NBController::class, 'hmCTWing4GWarm']);

// 海康指令解析测试
Route::get('/analyze/{string}', [NBController::class, 'analyze']);

// 大华指令解析测试
Route::get('dahua/analyze/{string}', [DaHuaController::class, 'analyze']);
Route::get('dahua/analyze2/{string}', [DaHuaController::class, 'analyze2']);
Route::get('dahua/analyze3/{string}', [DaHuaController::class, 'analyze3']);

// 六瑞解密

Route::post('excel', [ExcelController::class, 'handleImportExport']);
Route::get('hik', [HikvisionCloudController::class, 'index']);

Route::get('hikvision/addSubcription', [HikvisionCloudController::class, 'addSubcription']);
Route::get('hikvision/subcriptionList/{msgType?}', [HikvisionCloudController::class, 'subcriptionList']);
Route::post('hikvision/getTraditionMsg', [HikvisionCloudController::class, 'getTraditionMsg']);
Route::post('hikvision/getFireDeviceStatus', [HikvisionCloudController::class, 'getFireDeviceStatus']);
Route::post('hikvision/getParamConfig/{deviceID}', [HikvisionCloudController::class, 'getParamConfig']);
Route::post('hikvision/deviceTypeDict', [HikvisionCloudController::class, 'deviceTypeDict']);

Route::post('hikvision/callback/{code}', [HikvisionCloudController::class, 'callback']);

Route::prefix('liurui')->group(function () {
    Route::get('/muffling/{productId}/{deviceId}/{masterKey}', [LiuRuiController::class, 'muffling']);
    Route::any('/report', [LiuRuiController::class, 'report']);
    Route::get('toDecrypt/{string}', [LiuRuiController::class, 'toDecrypt']);
});
