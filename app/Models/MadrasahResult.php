<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class MadrasahResult extends Model
{
    use HasFactory, RecordsActivity, Searchable;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::addGlobalScope('student', function (Builder $builder){
           $builder->with('student', 'addedBy', 'approvedBy');
        });
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with('student');
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function searchableAs()
    {
        return 'madrash_result_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['name'] = $this->student?->name;
        $array['ssc_session'] = $this->student?->ssc_session;

        return $array;
    }


    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function addedBy(){
        return $this->belongsTo(User::class, 'added_by');
    }

    public function approvedBy(){
        return $this->belongsTo(User::class, 'approved_by');
    }

}
