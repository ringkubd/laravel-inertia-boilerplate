<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeMadrasa($query){
        return $query->with('madrasahResult')->whereNull('polytechnic')->where('madrasa_completed', 0)->where('status', 1);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePolytechnic($query){
        return $query->with('polytechnicResult')->whereNotNull('polytechnic')->where('madrasa_completed', 1)->where('status', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function users(){
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function madrasha(){
        return $this->belongsTo(Madrasha::class, 'madrasha_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function polytechnic(){
        return $this->belongsTo(Polytechnic::class, 'polytechnic', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function studentConfiguration(){
        return $this->hasOne(StudentConfiguration::class, 'users_id', 'users_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function results(){
        return $this->hasMany(Result::class, 'student_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function fees(){
        return $this->hasMany(Fee::class, 'session','polytechnic_session');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function classroom(){
        return $this->belongsToMany(ClassRoom::class,'class_room_students');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function madrashaSession(){
        return $this->belongsTo(AcademicSession::class, 'ssc_session', 'session');
    }

    public function polytechnicSession(){
        return $this->belongsTo(AcademicSession::class, 'polytechnic_session', 'session');
    }

    /**
     * @return HasOne
     */
    public function madrasahResult(){
        return $this->hasOne(MadrasahResult::class);
    }
    /**
     * @return HasOne
     */
    public function polytechnicResult(){
        return $this->hasMany(Result::class);
    }
}
