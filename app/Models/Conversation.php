<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, RecordsActivity, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => 'string',
    ];

    public function conversationUsers(){
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withTimestamps();
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function creator(){
        return $this->belongsTo(User::class, 'creator');
    }

    public function lastMessage(){
        return $this->hasOne(Message::class)->latest();
    }

    public function isOneToOne(){
        return $this->type === 'personal';
    }

    public function isGroup(){
        return $this->type === 'group';
    }

    public function isSupport(){
        return $this->type === 'support';
    }

    public function addParticipant($userId){
        return $this->conversationUsers()->attach($userId);
    }

    public function removeParticipant($userId){
        return $this->conversationUsers()->detach($userId);
    }

    public function getUnreadCount($userId){
        return $this->messages()
            ->whereNotIn('id', function($query) use ($userId) {
                $query->select('message_id')
                    ->from('message_reads')
                    ->where('user_id', $userId);
            })->count();
    }
}
