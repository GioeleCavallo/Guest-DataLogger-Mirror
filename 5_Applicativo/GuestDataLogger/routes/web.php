<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ApiCall;
use App\Http\Controllers\Settings;

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

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);
Route::get('/chart', [ChartController::class, 'index']);
Route::get('/admin', [LoginController::class, 'index']);
Route::post('/checkLogin', [LoginController::class, 'checkLogin']);

Route::get('/api/fetchData/',  [ApiCall::class, 'getData']);
Route::get('/api/fetchSettings/',  [Settings::class, 'getSettings']);
Route::post('/changeSettings',  [Settings::class, 'writeSettings']);
