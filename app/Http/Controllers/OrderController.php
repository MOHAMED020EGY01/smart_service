<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create(string $id)
    {
        $provider = User::find($id);
        if (!$provider) {
            return response()->json([
                "status" => "error",
                "message" => "Provider not found",
            ], 404);
        }
        $user = Auth::user();
        Order::create([
            "user_id" => $user->id,
            "provider_id" => $provider->id,
            "status" => "active",
        ]);
        return response()->json([
            "status"=> "success",
            "message"=> "Create order successfully",
        ], 201);
    }
}
