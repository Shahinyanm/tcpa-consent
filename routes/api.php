<?php

use App\Http\Controllers\Api\ConsentController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;

Route::post('/consents', [ConsentController::class, 'store']);
Route::post('/consents/{consent}/verify', [ConsentController::class, 'verifyCode']);
Route::post('/webhook/sms', [WebhookController::class, 'handleSmsResponse']);


Route::get('/companies', [CompanyController::class, 'index']);
Route::get('/companies/{hash}', [CompanyController::class, 'show']);
