<?php

use App\Http\Controllers\Admin\MaterialManufacturerController;
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
Route::any('/materialManufacturer/view', [MaterialManufacturerController::class, 'view']);
Route::post('/materialManufacturer/getList', [MaterialManufacturerController::class, 'getList']);
