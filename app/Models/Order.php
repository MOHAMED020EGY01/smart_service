<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'status',
    ];
    //** Get the user that owns the order.

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
