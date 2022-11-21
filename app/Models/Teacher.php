<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, RecordsActivity, SoftDeletes;

    protected static function booted()
    {
        $user_madrasha = auth()->user()?->madrasha_id;
        static::addGlobalScope('relation', function (Builder $builder)use($user_madrasha){
            if ($user_madrasha != null) {
                $builder->where('madrashas_id', auth()->user()->madrasha_id);
            }
            $builder->with('madrasa');
        });
    }

    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function trade(){
        return $this->belongsTo(Trade::class);
    }

    public function madrasa(){
        return $this->belongsTo(Madrasha::class, 'madrashas_id');
    }

    public function attendanceLog(){
        return $this->hasMany(TeacherAttendanceLog::class, 'user_id', 'users_id');
    }

    public function attendanceLogOneDay(){
        return $this->hasOne(TeacherAttendanceLog::class, 'user_id', 'users_id');
    }
}
