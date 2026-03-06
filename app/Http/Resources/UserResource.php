<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $rating = null;
        if ($this->role == "provider") {
            $this->loadMissing(['location', 'orders', 'rating']);
            $rate = 0;
            foreach ($this->rating as $rating) {
                $rate += $rating->rating;
            }
            $rating = [
                "rate" => $rate / ($this->rating->count() ?: 1),
                "count" => $this->rating->count(),
            ];
        } else {
            $this->loadMissing(['location', 'orders']);
        }

        return [
            "id" => $this->id ?? null,
            "name" => $this->name ?? null,
            "email" => $this->email ?? null,
            "role" => $this->role ?? null,
            "phone" => $this->phone ?? null,
            "category" => $this->Category ?? null,
            "experiences" => $this->Expert ?? null,

            "rating" => $rating,

            "statistics" => $this->orders ? [
                "totalNumberOfOrders" => $this->orders->count(),
                "finishedOrders" => $this->orders->where('status', 'completed')->count(),
            ] : null,

            "address" => $this->location ? [
                "id" => $this->location->id ?? null,
                "city" => $this->location->city ?? null,
                "street" => $this->location->street ?? null,
                "address_in_details" => $this->location->address_in_details ?? null,
            ] : null,
            "createdSince" => $this->created_at?->diffForHumans() ?? null,
        ];
    }
}
