<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = [
        'city',
        'street',
        'address_in_details',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
