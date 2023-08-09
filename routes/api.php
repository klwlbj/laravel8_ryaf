<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NBController;
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

Route::get('/nbWarm', [NBController::class, 'NbWarm']);
Route::post('/nbWarm', [NBController::class, 'received']);
