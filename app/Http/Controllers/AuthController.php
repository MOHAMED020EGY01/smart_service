<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\loginRequest;
use App\Http\Requests\Auth\registerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(loginRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'User login successfully',
                'data' => [
                    'user' => UserResource::make($user),
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);
        }
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function register(registerRequest $request)
    {
        $user = User::create($request->validated());
        if (Auth::attempt($request->validated())) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => UserResource::make($user),
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 201);
        }
        return response()->json([
            'status' => false,
            'message' => 'Email & Password does not match with our record.',
        ], 401);
    }
}
