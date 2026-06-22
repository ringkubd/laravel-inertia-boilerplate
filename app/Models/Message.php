<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, RecordsActivity, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'read_by' => 'array',
        'status' => 'string'
    ];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function sender(){
        return $this->hasOne(User::class, 'id', 'sender');
    }

    public function markAsDelivered(){
        return $this->update(['status' => 'delivered']);
    }

    public function markAsRead($userId){
        $readBy = $this->read_by ?? [];
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->update([
                'status' => 'read',
                'read_by' => $readBy
            ]);
        }
        return $this;
    }

    public function isRead($userId){
        return in_array($userId, $this->read_by ?? []);
    }

    public function isReadByAll(){
        $participants = $this->conversation->conversationUsers->pluck('id')->toArray();
        $readBy = $this->read_by ?? [];
        return count(array_intersect($participants, $readBy)) === count($participants);
    }
}
