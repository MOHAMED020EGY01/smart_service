<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = "messages";
    protected $fillable = [
        'chat_id',
        'messages',
    ];
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
}
