<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Location;
use App\Models\User;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = request()->user();


        return response()->json([
            "message" => "This is the profile page",
            "data" => [
                "user" => UserResource::make($user),
                "Categories" => User::CATEGORY,
            ],
        ]);
    }
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

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
                "Category" => $request->Category,
                "Expert" => $request->Expert,
            ]);
        }

        return response()->json([
            "message" => "Profile updated successfully",
            "data" => [
                "profile" => UserResource::make($user),
            ]
        ]);
    }
}
