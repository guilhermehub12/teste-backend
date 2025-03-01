<?php

declare(strict_types = 1);

use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;


Route::middleware(['cors:api', 'json.response'])->group( function () {
    Route::post('auth/register', [PassportAuthController::class, 'register'])
        ->middleware(ThrottleRequests::using('register'));
    Route::post('auth/login', [PassportAuthController::class, 'login'])
        ->middleware(ThrottleRequests::using('login'));
    Route::get('/', function () {
        return "APmmI";
    });
    Route::middleware('auth:api')->group( function () 
    {
        Route::post('auth/logout', [PassportAuthController::class, 'logout']);

        Route::resource('users', UserController::class);
        Route::resource('posts', PostController::class);
    })->middleware(ThrottleRequests::using('api'));
});
