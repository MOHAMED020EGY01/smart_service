<?php
namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function store(User $user){
        return $user->role == "user";
    }
    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function rate(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && $order->status === 'completed';
    }
}
