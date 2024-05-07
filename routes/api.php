<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Middleware\AdminToken;
use App\Http\Middleware\ProtectedRoute;

Route::post("/login", [AuthController::class, "login"]);

Route::prefix('users')->group(function () {
    Route::middleware([ProtectedRoute::class])->group(function () {
        Route::patch("/{email}/updatepassword", [UserController::class, "updatePassword"]);
    });
    Route::post("/{email}/saveCode", [UserController::class, "saveCode"]);
    Route::patch("/{email}/recoverpassword", [UserController::class, "recoverPassword"]);
});
