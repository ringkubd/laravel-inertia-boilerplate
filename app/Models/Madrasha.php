<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Madrasha extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity;

    protected $guarded = [];

    protected static function booted()
    {
        $user_madrasha = auth()->user()?->madrasha_id;
        static::addGlobalScope('relation', function (Builder $builder)use($user_madrasha){
            if ($user_madrasha != null) {
                $builder->where('madrashas.id', auth()->user()->madrasha_id);
            }
        });
    }

    public function students(){
        return $this->hasMany(Student::class);
    }

    public function teacherAttendance(){
        return $this->hasMany(TeacherAttendance::class, 'madrasha_id', 'id');
    }
}
