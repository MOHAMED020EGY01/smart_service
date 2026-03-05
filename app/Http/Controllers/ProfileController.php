<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = request()->user();
        return response()->json([
            "message" => "This is the profile page",
            "data" => [
                "user"=> UserResource::make($user),
                "Category"=>User::CATEGORY,
            ],
        ]);
    }
public function update(ProfileUpdateRequest $request)
{
    $user = $request->user();

    $user->update($request->validated());

    return response()->json([
        "message" => "Profile updated successfully",
        "data" => [
            "profile" => $user
        ]
    ]);
}
}
