<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ExtraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/user', [UserController::class, 'update']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/extras', [ExtraController::class, 'index']);

Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart', [CartController::class, 'store']);
Route::put('/cart/{itemId}', [CartController::class, 'update']);
Route::delete('/cart/clear', [CartController::class, 'clear']);
Route::delete('/cart/{itemId}', [CartController::class, 'destroy']);
Route::post('/cart/sync', [CartController::class, 'sync'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);

    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])
        ->middleware('admin');

    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
});