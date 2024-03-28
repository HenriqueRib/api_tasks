<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;

Route::group(['prefix' => 'users'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::delete('/delete', [AuthController::class, 'deleteAccount']);
});

Route::group(['prefix' => 'password'], function () {
    Route::post('/reset', [AuthController::class, 'resetPassword']);
});

Route::group(['prefix' => 'tasks'], function () {
    Route::get('/all', [ClientController::class, 'listAll']);
    Route::group(['prefix' => 'task'], function () {
        Route::post('/create', [ClientController::class, 'create']);
        Route::get('/{id}', [ClientController::class, 'show']);
        Route::put('/{id}/update', [ClientController::class, 'update']);
        Route::delete('/delete', [ClientController::class, 'delete']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
