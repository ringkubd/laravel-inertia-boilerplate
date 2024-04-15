<?php

namespace App\Models;

use App\Models\Traits\RecordsActivity;
use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;


class Student extends Model
{
    use HasFactory, SoftDeletes, RecordsActivity, Searchable, Compoships;

    protected $guarded = ['id'];

    protected static function booted()
    {
        $user_madrasha = auth()->user()?->madrasha_id;
        $student_madrasah = null;
        if (auth()->user()?->hasRole('student')) {
            $student_madrasah = auth()->user()->student_madrasah->first();
            $student_madrasah = $student_madrasah->id;
        }
        static::addGlobalScope('relation', function (Builder $builder)use($user_madrasha, $student_madrasah){
            if ($user_madrasha != null || $student_madrasah != null) {
                $builder->where('madrasha_id', auth()->user()->madrasha_id ?? $student_madrasah);
            }
            $builder->with(['madrasha', 'polytechnicInfo', 'classroom']);
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
        return $this->belongsToMany(ClassRoom::class,'class_room_students')->orderByDesc('name');
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
    public function madrasahResult(): HasOne
    {
        return $this->hasOne(MadrasahResult::class)->with('addedBy.roles', 'approvedBy.roles');
    }
    /**
     * @return \Awobaz\Compoships\Database\Eloquent\Relations\HasMany
     */
    public function polytechnicResult()
    {
        return $this->hasMany(Result::class)->with('addedBy.roles', 'approvedBy.roles');
    }

    /**
     * Invoice Details
     */
    public function invoice(){
        return $this->hasMany(Invoice::class, 'student_id', 'id');
    }

    public function invoiceDetails(){
        return $this->hasManyThrough(InvoiceDetail::class, Invoice::class)->with('invoice');
    }

    public function paymentSlip(){
        return $this->hasMany(PaymentSlip::class, 'student_id', 'student_id');
    }

    public function placements(): \Awobaz\Compoships\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Placement::class);
    }

}
