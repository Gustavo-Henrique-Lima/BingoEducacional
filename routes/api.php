<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Middleware\AdminToken;
use App\Http\Middleware\ProtectedRoute;

Route::post("/login", [AuthController::class, "login"]);

Route::prefix('users')->group(function () {
    Route::middleware([ProtectedRoute::class])->group(function () {
        Route::patch("/{email}/updatepassword", [UserController::class, "updatePassword"]);
    });
    Route::post("/{email}/saveRecoverPasswordCode", [UserController::class, "saveRecoverPasswordCode"]);
    Route::patch("/{email}/recoverpassword", [UserController::class, "recoverPassword"]);
    Route::post("/{email}/saveActivationCode", [UserController::class, "saveActivationCode"]);
    Route::patch("/{email}/activeAccount", [UserController::class, "activeAccount"]);
    Route::post("/saveUser", [UserController::class, "saveUser"]);
});

Route::prefix('categories')->group(function () {
    Route::middleware([AdminToken::class])->group(function () {
        Route::get("/getAll", [CategoryController::class, "getAllCategories"]);
    });
});

