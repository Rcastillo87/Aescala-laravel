<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\GeorreferenciaController;
use App\Http\Controllers\Api\DeviceController;


Route::get('/ping', function () {
    return response()->json(['ok' => config('app.api_secret_key')]);
});

Route::prefix('device')->middleware('hmac')->group(function () {

    Route::post('register', [DeviceController::class, 'register'])
        ->middleware('throttle:device-register');

    Route::post('location', [GeorreferenciaController::class, 'location'])
        ->middleware('throttle:device-location');

});


