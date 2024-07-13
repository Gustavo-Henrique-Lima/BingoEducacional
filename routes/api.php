<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\GameController;
use App\Http\Controllers\api\QuestionController;
use App\Http\Middleware\AdminToken;
use App\Http\Middleware\ProtectedRoute;

Route::post("/login", [AuthController::class, "login"]);

Route::prefix("users")->group(function () {
    Route::middleware([ProtectedRoute::class])->group(function () {
        Route::patch("/{email}/updatePassword", [UserController::class, "updatePassword"]);
    });
    Route::post("/{email}/saveRecoverPasswordCode", [UserController::class, "saveRecoverPasswordCode"]);
    Route::patch("/{email}/recoverpassword", [UserController::class, "recoverPassword"]);
    Route::post("/{email}/saveActivationCode", [UserController::class, "saveActivationCode"]);
    Route::patch("/{email}/activeAccount", [UserController::class, "activeAccount"]);
    Route::post("/saveUser", [UserController::class, "saveUser"]);
});

Route::prefix("categories")->group(function () {
    Route::middleware([AdminToken::class])->group(function () {
        Route::get("/getAll", [CategoryController::class, "getAllCategories"]);
        Route::post("/saveCategory", [CategoryController::class, "saveCategory"]);
    });
});

Route::prefix("question")->group(function () {
    Route::middleware([AdminToken::class])->group(function () {
        Route::post("/saveQuestion", [QuestionController::class, "saveQuestion"]);
        Route::get("/getByCategory/{categoryId}", [QuestionController::class, "getQuestionByCategory"]);
    });
});

Route::prefix("game")->group(function () {
    Route::middleware([AdminToken::class])->group(function () {
        Route::post("/createGame", [GameController::class, "createGame"]);
        Route::get("/myGames", [GameController::class, "getGameByOwner"]);
        Route::get("/{id}", [GameController::class, "getGameById"]);
    });
});
