<?php

use App\Http\Controllers\API\AuthApiController;
use App\Http\Controllers\API\WardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/email/resend', [AuthApiController::class, 'resendEmail'])
    ->name('verification.resend');

Route::get('/get-wards/{id}/', [WardController::class, 'getWards'])->name('get-wards');
