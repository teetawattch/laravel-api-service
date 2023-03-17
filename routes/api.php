<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::prefix('email')->group(function () {
    Route::post('send', [EmailController::class, 'sendEmail']);
    Route::get('gets', [EmailController::class, 'getAllOutbox']);

    Route::prefix('draft')->group(function () {
        Route::get('/gets', [EmailController::class, 'getDraft']);
        Route::post('/save', [EmailController::class, 'saveDraft']);
        Route::get('/get/{id}', [EmailController::class, 'getDraftById']);
    });
});
