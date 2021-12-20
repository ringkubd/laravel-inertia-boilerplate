<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Admission extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $guarded = ['id'];

    protected static function booted(){
        static::creating(function($admission){
            $admission->created_by = auth()->user()->id;
        });
        static::updating(function($admission){
            $admission->updated_by = auth()->user()->id;
        });

        static::addGlobalScope('relation', function (Builder $builder){
            $builder->with(['student', 'trade', 'polytechnic'])->orderByRaw("SUBSTRING_INDEX(academic_session,',',1) desc");
        });
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'admission_search_index';
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with('student')
            ->with('trade')
            ->with('polytechnic');
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
