<?php

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

Route::get('/install', [\Sayeed\ApplicationInstaller\Http\Controllers\InstallController::class, 'index']);
Route::post('/install/check-requirements', [\Sayeed\ApplicationInstaller\Http\Controllers\InstallController::class, 'checkServer']);
Route::post('/install/check-connection', [\Sayeed\ApplicationInstaller\Http\Controllers\InstallController::class, 'checkConnection']);
Route::post('/install/process', [\Sayeed\ApplicationInstaller\Http\Controllers\InstallController::class, 'process']);
