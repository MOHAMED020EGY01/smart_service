<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\loginRequest;
use App\Http\Requests\Auth\registerRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Google\Auth\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
use Google\Client as GoogleClient;

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
        ]);
        Log::info('Google Authentication Request', [
            'id_token' => $request->input('id_token'),
        ]);
        try {
            $googleClient = new GoogleClient([
                'client_id' => config('services.google.client_id'),
            ]);

            /*
             * Verify Google ID Token
             *
             * This checks the token signature and validates it against
             * the configured Google Client ID.
             */

            Log::info('Verifying Google ID Token', [
                'googleClient' => $googleClient,
            ]);
            $payload = $googleClient->verifyIdToken(
                $request->input('id_token')
            );
            if (!$payload) {
                Log::warning('Google Authentication Warning', [
                    'message' => 'Invalid Google token',
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Google token',
                ], 401);
            }

            /*
             * Required Google claims
             */
            if (
                empty($payload['sub']) ||
                empty($payload['email']) ||
                ($payload['email_verified'] ?? false) !== true
            ) {
                Log::warning('Google Authentication Warning', [
                    'message' => 'Invalid Google account data',
                    'payload' => $payload,
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Google account data',
                ], 401);
            }

            /*
             * Optional issuer validation
             */
            $validIssuers = [
                'https://accounts.google.com',
                'accounts.google.com',
            ];

            if (
                empty($payload['iss']) ||
                !in_array($payload['iss'], $validIssuers, true)
            ) {
                Log::warning('Google Authentication Warning', [
                    'message' => 'Invalid Google token issuer',
                    'issuer' => $payload['iss'] ?? null,
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Google token issuer',
                ], 401);
            }

            return DB::transaction(function () use ($payload) {

                /*
                 * Google "sub" is the stable unique identifier
                 * for the Google account.
                 */
                $googleId = $payload['sub'];
                Log::info('Google Authentication Payload', [
                    'google_id' => $googleId,
                    'email' => $payload['email'],
                    'name' => $payload['name'] ?? null,
                ]);
                $user = User::where('google_id', $googleId)
                    ->orWhere('email', $payload['email'])
                    ->first();

                if (!$user) {
                    $user = User::create([
                        'google_id' => $googleId,
                        'email' => $payload['email'],
                        'name' => $payload['name'] ?? 'Google User',
                        'password' => Hash::make(Str::random(32)),
                        'role' => User::ROLE['user'],
                    ]);
                } else {
                    /*
                     * Link Google account if the user already exists
                     * by email but has no google_id.
                     */
                    if (empty($user->google_id)) {
                        $user->update([
                            'google_id' => $googleId,
                        ]);
                    }

                    /*
                     * Optional: update profile data from Google.
                     */
                    if (
                        !empty($payload['name']) &&
                        $user->name !== $payload['name']
                    ) {
                        $user->update([
                            'name' => $payload['name'],
                        ]);
                    }
                }

                /*
                 * Create Laravel Sanctum token
                 */
                $token = $user
                    ->createToken('auth_token')
                    ->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => UserResource::make($user),
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                    ],
                ]);
            });

        } catch (\Throwable $e) {

            Log::error('Google Authentication Error', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Authentication failed',
            ], 401);
        }
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            $token = $user->currentAccessToken();
            if ($token instanceof PersonalAccessToken) {
                PersonalAccessToken::whereKey($token->getKey())->delete();
            }
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