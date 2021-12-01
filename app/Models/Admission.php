<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected static function booted(){
        static::creating(function($admission){
            $admission->created_by = auth()->user()->id;
        });
        static::updating(function($admission){
            $admission->updated_by = auth()->user()->id;
        });
    }

    public function student(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function trade(){
        return $this->belongsToMany(Trade::class, 'admission_trade_choice', 'admission_id', 'trade_id' );
    }

    public function polytechnic(){
        return $this->belongsToMany(Polytechnic::class, 'admission_polytechnic_choice', 'admission_id', 'polytechnic_id');
    }
}
