<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Middleware\DecryptApiRequest;
use App\Http\Middleware\EncryptApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});

Route::group(
    // ['middleware' => [EncryptApiResponse::class, DecryptApiRequest::class]],
    function ($router) {
        Route::get('/deliveries', [DeliveryController::class, 'index']);
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/transaction', [TransactionController::class, 'store']);
    }
);
