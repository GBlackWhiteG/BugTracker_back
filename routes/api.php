<?php

use App\Http\Controllers\BugController;
use App\Http\Controllers\BugHistoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ManagerMiddleware;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use BeyondCode\LaravelWebSockets\WebSockets\WebSocketHandler;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

WebSocketsRouter::webSocket('/app/{appKey}', WebSocketHandler::class);

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
        Route::get('/bugs/{bug}', 'show');
        Route::post('/bugs', 'store')->middleware([AdminMiddleware::class, ManagerMiddleware::class]);
        Route::post('/bugs/{bug}', 'update')->middleware([AdminMiddleware::class, ManagerMiddleware::class]);
        Route::post('/bugs/fields/{bug}', 'changeField');
        Route::delete('/bugs/{bug}', 'destroy')->middleware([AdminMiddleware::class, ManagerMiddleware::class]);
        Route::delete('/bugs/file/{file}', 'destroyFile')->middleware([AdminMiddleware::class, ManagerMiddleware::class]);
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/profile/{user}', 'show');
        Route::get('/user-suggestions', 'suggestions');
    });

    Route::get('/bug-history/{id}', [BugHistoryController::class, 'index']);

    Route::controller(CommentController::class)->group(function () {
       Route::post('/comments', 'store');
       Route::post('/comments/{comment}', 'update');
       Route::delete('/comments/{comment}', 'destroy');
       Route::delete('/comments/file/{file}', 'destroyFile');
    });
});

Route::get('/search', [SearchController::class, 'search']);

Route::get('/email/verify/{id}/{hash}', [VerifyController::class, 'verifyEmail'])
    ->middleware('throttle:6,1')->name('verification.verify');
