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
        return $query->whereNull('polytechnic')->where('madrasa_completed', 0)->where('status', 1);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePolytechnic($query){
        return $query->whereNotNull('polytechnic')->where('madrasa_completed', 1)->where('status', 1);
    }

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function madrasha(){
        return $this->belongsTo(Madrasha::class, 'madrasha_id', 'id');
    }

    public function polytechnic(){
        return $this->belongsTo(Polytechnic::class, 'polytechnic', 'id');
    }

    public function studentConfiguration(){
        return $this->hasOne(StudentConfiguration::class, 'users_id', 'users_id');
    }

    public function results(){
        return $this->hasMany(Result::class, 'student_id', 'id');
    }

    public function fees(){
        return $this->hasMany(Fee::class, 'session','polytechnic_session');
    }

    public function classroom(){
        return $this->belongsToMany(ClassRoom::class,'class_room_students');
    }
}
