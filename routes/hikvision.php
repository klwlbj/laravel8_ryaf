<?php

use App\Http\Controllers\Hikvision\UnitsController;
use Illuminate\Support\Facades\Route;

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
Route::post('/units/add', [UnitsController::class, 'add']);
