<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        // نرجع المصفوفة مباشرة فقط
        return $this->collection->map(function ($user) {
            return [
                'id'       => $user->id,
                'name'     => $user->name,
                'role'     => $user->role,
                'phone'    => $user->phone,
                'category' => $user->category,
                'rating'   => round($user->rate ?? 0, 2),
            ];
        })->toArray(); // تحويل النتيجة لمصفوفة عادية
    }
}
