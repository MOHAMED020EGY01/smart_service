<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = "chats";
    protected $fillable = [
        'user_id',
        'provider_id',
        'last_message',
        'User_name',
        'Provider_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id','id');
    }
    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id','id');
    }

}
