<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder;
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
        'rate',
        'category',
        'experiences',
    ];
    public const ROLE = [
        'user' => 'User',
        'provider' => 'Provider',
    ];
    public const CATEGORY = [
        'البرمجة' => 'البرمجة',
        'البناء' => 'البناء',
        'السباكة' => 'السباكة',
        'الكهرباء' => 'الكهرباء',
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
    public function scopeIsProvider(Builder $query)
    {
        return $query->where('role', 'provider');
    }
    public function scopeChat(Builder $query,string $role)
    {
        if($role === 'provider'){
            return $query->where('role', 'provider')->with('chat_user');
        }
        return $query->where('role', 'user')->with('chat_provider');
    }


    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function UserOrders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function ProviderOrders()
    {
        return $this->hasMany(Order::class, 'provider_id', 'id');
    }

    public function orders()
    {
        if ($this->role === 'provider') {
            return $this->ProviderOrders();
        }
        return $this->UserOrders();
    }

    public function chat_user()
    {
        return $this->hasMany(Chat::class, 'user_id', 'id');
    }
    public function chat_provider()
    {
        return $this->hasMany(Chat::class, 'provider_id', 'id');
    }


}
