<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'users_id' => $this->users_id,
            'user' => $this->user,
            'name' => $this->name,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'mobile' => $this->mobile,
            'joining_date' => $this->joining_date,
            'designation' => $this->designation,
            'trade_id' => $this->trade_id,
            'madrashas_id' => $this->madrashas_id,
            'dob' => $this->dob,
            'photo' => $this->photo,
            'nid' => $this->nid,
            'bank_account' => $this->bank_account,
            'bank_branch' => $this->bank_branch,
            'bank_name' => $this->bank_name,
            'present_address' => $this->present_address,
            'permanent_address' => $this->permanent_address,
            'attendanceLogOneDay' => new TeachersAttendanceResource($this->attendanceLogOneDay),
        ];
    }
}
