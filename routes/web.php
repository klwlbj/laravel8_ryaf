<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::any('imeiCheck/view', [\App\Http\Controllers\IMEICheckController::class, 'view']);


//Route::get('/myCommand', function () {
//    print_r(Artisan::call('storage:link'));die;
//});
