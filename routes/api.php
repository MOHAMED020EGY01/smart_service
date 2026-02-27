<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return response()->json([
        "message" => "Welcome to the API"
    ]);
});


Route::post("/register", [\App\Http\Controllers\AuthController::class, "register"]);
Route::post("/login", [\App\Http\Controllers\AuthController::class, "login"]);
