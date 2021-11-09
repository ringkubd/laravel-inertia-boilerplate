<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportConversationMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted() {
        static::addGlobalScope('relation', function (Builder $builder){
            $builder->with('sender', 'whosend');
        });
    }

    public function sender(){
        return $this->belongsTo(User::class, 'sender');
    }
    public function whosend(){
        return $this->belongsTo(User::class, 'sender');
    }

    public function conversation(){
        return $this->belongsTo(SupportConversation::class, 'support_conversation_id');
    }
}
