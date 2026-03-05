<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'location_id',
        'phone',
        'Category',
        'Expert',
    ];
    public const ROLE = [
        'user' => 'User',
        'provider' => 'Provider',
    ];
    public const CATEGORY = [
        'plumbing' => 'Plumbing',
        'electrical' => 'Electrical',
        'cleaning' => 'Cleaning',
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'provider_token'
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'provider_token' => 'encrypted',
        ];
    }
    //* Scopes that should be cast to native types.
    public function scopeIsProvider($query)
    {
        return $query->where('role', self::ROLE['provider']);
    }


    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
