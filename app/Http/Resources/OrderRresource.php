<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderRresource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => $this->status,
            'phone_user' => $this->phone_user,
            'provider_name' => $this->provider?->name,
            'user_name' => $this->user?->name,
            'rating' => $this->rating,
            'description' => $this->description,
            'updated_at' => $this->updated_at->diffForHumans() ?? null,
        ];
    }
}
