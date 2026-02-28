<?php

use App\Http\Controllers\AuthController;
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
    }
);


Route::group(
    [
        "middleware" => "auth:sanctum"
    ],
    function () {
        Route::get('getUser', [AuthController::class, 'getUser']);
        Route::delete('logout', [AuthController::class,'logout']);
    });
