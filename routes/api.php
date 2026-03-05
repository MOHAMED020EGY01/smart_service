<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return response()->json([
        "message" => "Welcome to the API"
    ]);
});

Route::group(
    [
        "middleware" => "guest"
    ],
    function () {
        Route::post("/register", [AuthController::class, "register"]);
        Route::post("/login", [AuthController::class, "login"]);
        Route::post("/google", [AuthController::class, "google"]);
    }
);


Route::group(
    [
        "middleware" => "auth:sanctum"
    ],
    function () {
        Route::get('profile', [ProfileController::class,'profile']);
        Route::put('profile', [ProfileController::class,'update']);

        Route::get('getUser', [AuthController::class, 'getUser']);
        Route::delete('logout', [AuthController::class, 'logout']);
    }
);
