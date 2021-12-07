<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;


class Student extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity, Searchable;

    protected $guarded = ['id'];

    protected static function booted()
    {
        $user_madrasha = auth()->user()?->madrasha_id;
        static::addGlobalScope('relation', function (Builder $builder)use($user_madrasha){
            if ($user_madrasha != null) {
                $builder->where('madrasha_id', auth()->user()->madrasha_id);
            }
            $builder->with('madrasha');
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
        return $query->with('classroom');
    }

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

    /**
     * Invoice Details
     */
    public function invoice(){
        return $this->hasMany(Invoice::class, 'student_id', 'id');
    }

}
