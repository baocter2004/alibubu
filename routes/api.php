<?php

use App\Const\SecurityConst;
use App\Http\Controllers\API\WardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/get-wards/{id}/', [WardController::class, 'getWards'])
    ->middleware('throttle:' . SecurityConst::LIMITER_API)
    ->name('get-wards');
