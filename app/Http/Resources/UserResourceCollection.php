<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $rating = null;
        $this->loadMissing(['orders']);
        
        if ($this->role == "provider") {
            $rate = 0;
            foreach ($this->orders as $order) {
                $rate += $order->rating;
            }
            $rating = [
                "rate" => round($rate / ($this->orders->count() ?: 1), 2),
                "count" => $this->orders->count(),
            ];
        }

        return [
            "id" => $this->id ?? null,
            "name" => $this->name ?? null,
            "role" => $this->role ?? null,
            "phone" => $this->phone ?? null,
            "category" => $this->Category ?? null,
            "rating" => $rating,
        ];
    }
}
