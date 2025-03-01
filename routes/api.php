<?php

use App\Http\Controllers\BugController;
use App\Http\Controllers\BugHistoryController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
});

Route::middleware('auth:api')->group(function () {
    Route::controller(BugController::class)->group(function () {
        Route::get('/bugs', 'index');
        Route::post('/bugs', 'store');
        Route::patch('/bugs/{bug}', 'changeField');
    });

    Route::get('/bug-history/{id}', [BugHistoryController::class, 'index']);
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->name('verification.verify');
