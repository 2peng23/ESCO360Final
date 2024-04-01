<?php

use App\Http\Controllers\DeviceController;
use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::prefix('api')->middleware(AdminOnly::class)->group(function() {
    Route::get('/devices', [DeviceController::class, 'devices'])->name('devices');
    Route::get('/add-account', [DeviceController::class, 'addAccount'])->name('add-account');
    Route::get('/device-info/{deviceCID}', [DeviceController::class, 'deviceInformation'])->name('device-info');
});