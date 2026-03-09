<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\loginRequest;
use App\Http\Requests\Auth\registerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Google_Client;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;


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
        return DB::transaction(function () use ($request) {
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
        });
    }

    public function register(registerRequest $request)
    {
        return DB::transaction(function () use ($request) {
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
        });
    }

    public function google(Request $request)
    {
        $request->validate([
            'id_token' => ['required', 'string'],
            'role' => ['nullable', Rule::in(array_keys(User::ROLE))],
        ]);

        $client = new Google_Client(['client_id' => config('services.google.client_id')]);

        if (app()->environment('local')) {
            $client->setHttpClient(new Client(['verify' => false]));
        }

        try {
            $payload = $client->verifyIdToken($request->id_token);
            Log::info('Google token verification payload', ['payload' => $payload]);
            if (!$payload || !isset($payload['email'])) {
                Log::info('Google token verification failed', ['payload' => $payload]);
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Google token or insufficient scopes'
                ], 401);
            }

            return DB::transaction(function () use ($payload, $request) {
                $user = User::firstOrCreate(
                    ['email' => $payload['email']],
                    [
                        'name' => $payload['name'] ?? 'Google User',
                        'password' => Hash::make(Str::random(16)),
                        'role' => $request->input('role', User::ROLE['user']),
                    ]
                );

                $token = $user->createToken('auth_token')->plainTextToken;
                Log::info('Google authentication successful', ['user_id' => $user->id, 'email' => $user->email]);
                return response()->json([
                    'status' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => UserResource::make($user),
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                    ]
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Authentication failed'
            ], 500);
        }
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
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
