<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            "message" => "This is the profile page",
            "data" => [
                "user" => UserResource::make($user),
                "categories" => User::CATEGORY,
            ],
        ]);
    }
    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $location = null;
        if ($request->has('city') || $request->has('street') || $request->has('address_in_details')) {
            $location = Location::updateOrCreate(
                ["id" => $user->location_id],
                [
                    "city" => $request->city,
                    "street" => $request->street,
                    "address_in_details" => $request->address_in_details,
                ]
            );
        }
        $user->update([
            "location_id" => $location?->id,
            "name" => $request->name,
            "phone" => $request->phone,
        ]);

        if ($user->role == "provider") {
            $user->update([
                "category" => $request->category,
                "experiences" => $request->experiences,
            ]);
        }

        return response()->json([
            "message" => "Profile updated successfully",
            "data" => [
                "profile" => UserResource::make($user),
            ]
        ],200);
    }
}
