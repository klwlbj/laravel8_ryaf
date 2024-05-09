<?php

use App\Http\Controllers\Platform\AuthController;
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
Route::group(['middleware' => ['platformCheckSign']],function(){
    Route::get('/management/operatorAPIToken', [AuthController::class, 'operatorAPIToken']);
});
//需要token的接口
Route::group(['middleware' => ['platformCheckToken','platformCheckSign']],function(){

});
