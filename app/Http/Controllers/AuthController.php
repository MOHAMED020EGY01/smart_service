<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\loginRequest;
use App\Http\Requests\Auth\registerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Socialite;

class AuthController extends Controller
{
    public function getUser(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken || !$accessToken->tokenable) {
            return response()->json(['message' => 'Invalid or expired token'], 401);
        }
        $user = $accessToken->tokenable;
        return response()->json([
            'status' => true,
            'data' => [
                'user' => UserResource::make($user)
            ]
        ], 200);
    }

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
        return response()->json([
            'status' => false,
            'message' => 'Email & Password does not match with our record.',
        ], 401);
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

    public function logout()
    {
        $user = Auth::guard('sanctum')->user();
        if($user){
            $user->currentAccessToken()->delete();
            return response()->json([
                'status' => true,
                'message' => 'User logged out successfully',
            ], 200);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not authenticated',
        ], 401);
    }
}
