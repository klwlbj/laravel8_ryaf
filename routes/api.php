<?php

use App\Http\Controllers\HaimanController;
use App\Http\Controllers\TpsonController;
use App\Http\Controllers\LiangXinController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NBController;
use App\Http\Controllers\HikvisionSmoke;
use App\Http\Controllers\DaHuaController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\HaoenController;
use App\Http\Controllers\CTWingController;
use App\Http\Controllers\LiuRuiController;
use App\Http\Controllers\OneNetController;
use App\Http\Controllers\YuanLiuController;
use App\Http\Controllers\WanLinYunController;
use App\Http\Controllers\LiuRuiCloudController;
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
    Route::get('/writeResource/{imei}/{args}/{dwPackageNo}', [OneNetController::class, 'customWriteResource']);
    Route::get('/execute/{imei}/{args}/{dwPackageNo}', [OneNetController::class, 'execute']);
    Route::get('/realTimewriteResource/{imei}/{args}/{dwPackageNo}', [OneNetController::class, 'realTimewriteResource']);
    Route::get('/logQuery/{imei}/{uuid}', [OneNetController::class, 'logQuery']);
    Route::get('/deviceInfo/{projectId}/{imei}', [OneNetController::class, 'deviceInfo']);
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
    Route::get('/createCommand/{productId}/{deviceId}/{masterKey}/{command}/{dwPackageNo}', [CTWingController::class, 'createCommand']);// 4g用
    Route::get('/createNTTCommand/{productId}/{deviceId}/{masterKey}/{second}', [CTWingController::class, 'createNTTCommand']);
});

// http回调
Route::get('/nbWarm', [NBController::class, 'nbWarm']);
Route::get('/hkWarm', [NBController::class, 'nbWarm']);
Route::get('/hmOneNet4GWarm', [NBController::class, 'nbWarm']);
Route::get('/hmOneNetInfraredWarm', [NBController::class, 'nbWarm']);
Route::post('/nbWarm', [NBController::class, 'nbReceived']);

// 海康烟感回调
Route::post('/hkWarm', [HikvisionSmoke::class, 'hkOnenetWarm']);// 移动
Route::post('/hkCTWingWarm', [HikvisionSmoke::class, 'hkCTWingWarm']);
Route::post('/hkCTWing4GWarm', [HikvisionSmoke::class, 'hkCTWing4GWarm']);
// 拓深智慧用电平台
Route::prefix('transfer')->name('tpson')->group(function () {
    Route::post('/device/data', [TpsonController::class, 'data']);
    Route::post('/device/fault', [TpsonController::class, 'fault']);
    Route::post('/device/alarm ', [TpsonController::class, 'alarm']);
    Route::post('/device/config', [TpsonController::class, 'config']);
    Route::post('/insert/{imei}/{nodeId}', [TpsonController::class, 'importDevice']);

});
// 海曼烟感回调
Route::post('/hmCTWingInfraredWarm', [HaimanController::class, 'hmCTWingInfraredWarm']);
Route::post('/hmOneNet4GWarm', [HaimanController::class, 'hmOneNet4GWarm']);
Route::post('/hmOneNetInfraredWarm', [HaimanController::class, 'hmOneNetInfraredWarm']);
Route::post('/insertSmokeDetector/{imei}', [HaimanController::class, 'insertSmokeDetector']);

Route::post('/dhCTWingWarm', [DaHuaController::class, 'dhCTWingWarm']);

Route::any('/hmCTWing4GWarm/{string}', [NBController::class, 'hmCTWing4GWarm']);

// 海康指令解析测试
Route::get('hikvision/analyze/{string}', [HikvisionSmoke::class, 'analyze']);

// 大华指令解析测试
Route::get('dahua/analyze/{string}', [DaHuaController::class, 'analyze']);
Route::get('dahua/analyze2/{string}', [DaHuaController::class, 'analyze2']);
Route::get('dahua/analyze3/{string}', [DaHuaController::class, 'analyze3']);

Route::post('excel', [ExcelController::class, 'handleImportExport']);

// 海康云平台
Route::get('hik', [HikvisionCloudController::class, 'index']);
Route::post('hikvision/addSubcription', [HikvisionCloudController::class, 'addSubcription']);
Route::post('hikvision/subcriptionList/{msgType?}', [HikvisionCloudController::class, 'subcriptionList']);
Route::post('hikvision/getTraditionMsg', [HikvisionCloudController::class, 'getTraditionMsg']);
Route::post('hikvision/getFireDeviceStatus', [HikvisionCloudController::class, 'getFireDeviceStatus']);
Route::post('hikvision/getParamConfig/{deviceID}', [HikvisionCloudController::class, 'getParamConfig']);
Route::post('hikvision/deviceTypeDict', [HikvisionCloudController::class, 'deviceTypeDict']);
Route::post('hikvision/deleteDevice', [HikvisionCloudController::class, 'deleteDevice']);
Route::post('hikvision/addVideoDevice', [HikvisionCloudController::class, 'addVideoDevice']);
Route::post('hikvision/deleteVideoDevice', [HikvisionCloudController::class, 'deleteVideoDevice']);
Route::post('hikvision/getCameraPlayURL', [HikvisionCloudController::class, 'getCameraPlayURL']);
Route::post('hikvision/getAlarm', [HikvisionCloudController::class, 'getAlarm']);
Route::post('hikvision/getCamera', [HikvisionCloudController::class, 'getCamera']);
Route::post('hikvision/getVideoDevice', [HikvisionCloudController::class, 'getVideoDevice']);
Route::post('hikvision/receiveAlarm', [HikvisionCloudController::class, 'receiveAlarm']);

Route::post('hikvision/callback/{code}', [HikvisionCloudController::class, 'callback']);

Route::prefix('liurui')->group(function () {
    Route::get('/muffling/{productId}/{deviceId}/{masterKey}', [LiuRuiController::class, 'muffling']);
    Route::get('/mufflingByOneNet/{imei}', [LiuRuiController::class, 'mufflingByOneNet']);
    Route::any('/report', [LiuRuiController::class, 'report']);
    Route::any('/oneNetReport', [LiuRuiController::class, 'oneNetReport']);
    Route::get('toDecrypt/{string}', [LiuRuiController::class, 'toDecrypt']);
});

Route::prefix('yuanliu')->group(function () {
    # 消音
    Route::get('/muffling/{productId}/{deviceId}/{masterKey}', [YuanLiuController::class, 'muffling']);
//    Route::get('/mufflingByOneNet/{imei}', [YuanLiuController::class, 'mufflingByOneNet']);
    # 设置阈值
    Route::get('/setThreshold/{productId}/{deviceId}/{masterKey}/{alarmValue}', [YuanLiuController::class, 'setThreshold']);
    # 设置报警检测时间
    Route::get('/setDetectionTime/{productId}/{deviceId}/{masterKey}/{time}', [YuanLiuController::class, 'setDetectionTime']);
    # 设置永久消声
    Route::get('/setSilencing/{productId}/{deviceId}/{masterKey}/{state}', [YuanLiuController::class, 'setSilencing']);
    #设置温度阈值
    Route::get('/setTempThreshold/{productId}/{deviceId}/{masterKey}/{value}', [YuanLiuController::class, 'setTempThreshold']);
    Route::any('/report', [YuanLiuController::class, 'report']);
    Route::any('/oneNetReport', [YuanLiuController::class, 'oneNetReport']);
    Route::get('/mufflingByOneNet/{imei}', [YuanLiuController::class, 'mufflingByOneNet']);
    Route::get('/setThresholdByOneNet/{imei}/{alarmValue}', [YuanLiuController::class, 'setThresholdByOneNet']);
    Route::get('/setDetectionTimeByOneNet/{imei}/{time}', [YuanLiuController::class, 'setDetectionTimeByOneNet']);
    Route::get('/setTempThresholdByOneNet/{imei}/{value}', [YuanLiuController::class, 'setTempThresholdByOneNet']);
});

Route::prefix('liuruicloud')->group(function () {
    Route::any('/report', [LiuRuiCloudController::class, 'report']);
});

Route::prefix('haoen')->group(function () {
    Route::get('/createCmdCommand/{productId}/{deviceId}/{masterKey}/{cmdType}', [HaoenController::class, 'createCmdCommand']);
});

// 豪恩声光报警器
Route::post('/haoenCtwing', [HaoenController::class, 'haoenSoundLigntAlarm']);
// 豪恩手动报警器
Route::post('/haoen2Ctwing', [HaoenController::class, 'haoenManualAlarm']);

Route::get('/xiaohui/toDecrypt/{string}', [LiuRuiController::class, 'xiaohuiToDecrypt']);

Route::post('/queryImei', [\App\Http\Controllers\IMEICheckController::class, 'queryImei'])->name('submit.form');

Route::prefix('haiman')->group(function () {
    Route::get('/insertSmokeDetector/{imei}', [HaimanController::class, 'insertSmokeDetector']);
    Route::get('/insertGasDetector/{imei}', [HaimanController::class, 'insertGasDetector']);
    Route::any('/mufflingByOneNet/{imei}', [HaimanController::class, 'mufflingByOneNet']);
    Route::get('/mufflingByCTWing/{productId}/{deviceId}/{masterKey}', [HaimanController::class, 'mufflingByCTWing']);

    // 电信NB下发命令
    // 示例：http://ryaf.laravel.com/api/haiman/createCMDByCTWing/17189257/868550069363245/5e9dffc817974731a3b610e6c5ca6e51/1F5E00020019
    Route::get('/createCMDByCTWing/{productId}/{imei}/{masterKey}/{cmd}', [HaimanController::class, 'createCMDByCTWing']);
    Route::get('/decode/{code}', [HaimanController::class, 'decode']);
    // 1F5F000400000500 布防时间,0点到5点
    // 1F5D000101 高灵敏度
    // 1F650001 一直布防
    // 1F5E00020019 上报无人时间，0.25小时
    Route::get('/writeResource/{imei}/{cmd}', [OneNetController::class, 'writeResource']);

});

Route::get('/migrationTest', [\App\Http\Controllers\BaseController::class, 'migrationTest']);

