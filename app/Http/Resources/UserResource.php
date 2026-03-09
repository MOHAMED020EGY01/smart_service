<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $rating = null;
        $this->loadMissing(['location', 'ProviderOrders','UserOrders']);
        if ($this->role == "provider") {
            $rating = [
                "rate" => round($this->rate, 2),
                "count" => $this->orders->count(),
            ];
        }

        return [
            "id" => $this->id ?? null,
            "name" => $this->name ?? null,
            "email" => $this->email ?? null,
            "role" => $this->role ?? null,
            "phone" => $this->phone ?? null,
            "category" => $this->category ?? null,
            "experiences" => $this->experiences ?? null,

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
