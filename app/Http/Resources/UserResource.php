<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id ?? null,
            "name"=> $this->name ?? null,
            "email"=> $this->email ?? null,
            "role"=> $this->role ?? null,
            "phone"=> $this->phone ?? null,
            "Category"=> $this->Category ?? null,
            "Expert"=> $this->Expert ?? null,
            "rating"=> $this->rating ?? null,
            "statistics" => $this->orders ? [
                "totalNumberOfOrders" => $this->orders->count(),
                "completedNumberOfOrders" => $this->orders->where('status', 'completed')->count(),
            ] : null,
            "location"=> $this->location ? [
                "id"=> $this->location->id,
                "city"=> $this->location->city,
                "street"=> $this->location->street,
                "address_in_details"=> $this->location->address_in_details,
            ] : null,
            "created_at"=> $this->created_at ?? null,
            "updated_at"=> $this->updated_at ?? null,

        ];
    }
}
