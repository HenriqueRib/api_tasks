<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;

Route::get('teste', [ClientController   ::class, 'teste']);
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
    Route::post('/create', [ClientController::class, 'createTask']);
    Route::put('/{id}/update', [ClientController::class, 'updateTask']);
    Route::delete('/delete', [ClientController::class, 'deleteTask']);
    Route::get('/{id}', [ClientController::class, 'showTask']);
    Route::get('/all', [ClientController::class, 'listAllTasks']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
