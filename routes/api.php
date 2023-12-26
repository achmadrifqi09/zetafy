<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\UserController;
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

Route::group(['prefix' => 'v1', 'middleware' => 'verify_app_key'], function () {
    Route::post('/users', [UserController::class, 'register']);
    Route::post('users/login', [UserController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('/users/current', [UserController::class, 'get']);
        Route::put('/users/current', [UserController::class, 'update']);
        Route::put('/users/current/password', [UserController::class, 'updatePassword']);
        Route::post('/users/current/avatar', [UserController::class, 'updateAvatar']);
        Route::post('/users/current/delete', [UserController::class, 'destroy']);
        Route::get('/users/logout', [UserController::class, 'logout']);

        Route::post('/users/addresses', [AddressController::class, 'create']);
        Route::get('/users/addresses', [AddressController::class, 'list']);
        Route::get('/users/addresses/{id}', [AddressController::class, 'get']);
        Route::put('/users/addresses/{id}', [AddressController::class, 'update']);
        Route::delete('/users/addresses/{id}', [AddressController::class, 'destroy']);
    });

    Route::group(['middleware' => ['auth:sanctum', 'administrator_owner_permission']], function () {
    });

    Route::group(['middleware' => ['auth:sanctum', 'owner_permission']], function () {
        Route::get('/apps', [AppController::class, 'list']);
        Route::post('/apps', [AppController::class, 'create']);
        Route::delete('/apps/{id}', [AppController::class, 'destroy']);

        Route::get('/users', [UserController::class, 'list']);
    });
});
