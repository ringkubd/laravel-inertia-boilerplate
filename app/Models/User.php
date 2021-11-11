<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'madrasha_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted()
    {
        static::addGlobalScope("relation", function (Builder $builder){
            $builder->with('madrasah', 'student','teacher');
        });
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function conversation(){
        return $this->belongsToMany(Conversation::class, 'conversation_user')->whereHas('conversationUsers', function ($q){
            $q->where('user_id', auth()->user()->id ?? null);
        })->with('conversationUsers')->with('messages');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function teacher(){
        return $this->hasOne(Teacher::class, 'users_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function student(){
        return $this->hasOne(Student::class, 'users_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function madrasah(){
        return $this->belongsTo(Madrasha::class, 'madrasha_id');
    }

    public function activeSupport(){
        return $this->hasOne(SupportConversation::class, 'creator')->where('status', 0);
    }

    /**
     * Get the activity timeline for the user.
     *
     * @return mixed
     */
    public function activity()
    {
        return $this->hasMany(Activity::class)
            ->with(['user', 'subject'])
            ->latest();
    }

}
