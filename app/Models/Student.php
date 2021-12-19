<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Laravel\Scout\Searchable;


class Student extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity, Searchable, Compoships;

    protected $guarded = ['id'];

    protected static function booted()
    {
        $user_madrasha = auth()->user()?->madrasha_id;
        static::addGlobalScope('relation', function (Builder $builder)use($user_madrasha){
            if ($user_madrasha != null) {
                $builder->where('madrasha_id', auth()->user()->madrasha_id);
            }
            $builder->with(['madrasha', 'polytechnicInfo']);
        });
    }

    /**
     * Get the name of the index associated with the model.
     *
     * @return string
     */
    public function searchableAs()
    {
        return 'students_index';
    }

    /**
     * Modify the query used to retrieve models when making all of the models searchable.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeAllSearchableUsing($query)
    {
        return $query->with('classroom')
        ->with('polytechnicInfo')
        ->with('results')
        ->with('madrashaSession')
        ->with( 'polytechnicSession')
        ->with( 'madrasahResult')
        ->with( 'polytechnicResult')
        ->with('madrasha');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['polytechnic_name'] = $this->polytechnic?->name;
        $array['madrasah_name'] = $this->madrasha?->name;

        return $array;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeMadrasa($query){
        return $query->with('madrasahResult')->whereNull('polytechnic_id')->where('madrasa_completed', 0)->where('status', 1);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePolytechnic($query){
        return $query->with('polytechnicResult')->whereNotNull('polytechnic_id')->where('madrasa_completed', 1)->where('status', 1);
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
        return $this->belongsTo(Polytechnic::class, 'polytechnic_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function polytechnicInfo(){
        return $this->belongsTo(Polytechnic::class, 'polytechnic_id', 'id');
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
        return $this->hasMany(Fee::class, ['session', 'trade'],['polytechnic_session', 'polytechnic_trade_id']);
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

    /**
     * Invoice Details
     */
    public function invoice(){
        return $this->hasMany(Invoice::class, 'student_id', 'id');
    }

}
