<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = ['id'];

    public function conversationUsers(){
        return $this->belongsToMany(User::class, 'conversation_user');
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }
}
