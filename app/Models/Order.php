<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'rating',
        'status',
    ];
    //** Get the user that owns the order.

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

}
