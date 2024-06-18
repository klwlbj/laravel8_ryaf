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
//物品厂家
Route::any('/materialManufacturer/view', [MaterialManufacturerController::class, 'view']);
Route::post('/materialManufacturer/getList', [MaterialManufacturerController::class, 'getList']);
Route::post('/materialManufacturer/getInfo', [MaterialManufacturerController::class, 'getInfo']);
Route::post('/materialManufacturer/getAllList', [MaterialManufacturerController::class, 'getAllList']);
Route::post('/materialManufacturer/add', [MaterialManufacturerController::class, 'add']);
Route::post('/materialManufacturer/update', [MaterialManufacturerController::class, 'update']);
Route::post('/materialManufacturer/delete', [MaterialManufacturerController::class, 'delete']);
