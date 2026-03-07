<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{
    public function index()
    {
        $this->checkAuth();
        $providers = User::isProvider()->get();
        return response()->json(
            [
                "message" => "List of providers",
                "data" => [
                    "providers" => new UserResourceCollection($providers),
                ]
            ],
            200
        );
    }
    public function show(string $id)
    {
        $this->checkAuth();
        $provider = User::where('id', $id)->isProvider()->first();
        if (!$provider) {
            return response()->json(
                [
                    "message" => "Provider not found",
                ],
                404
            );
        }
        return response()->json(
            [
                "message" => "Provider details",
                "data" => [
                    "provider" => UserResource::make($provider),
                ]
            ],
            200
        );
    }

    public function checkAuth(){
        $user = Auth::user();
        if ($user->role === 'provider') {
            return response()->json(
                [
                    "message" => "You are not authorized to access this resource",
                ],
                403
            );
        }
    }
}
