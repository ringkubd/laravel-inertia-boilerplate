<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TeachersAttendanceResource extends JsonResource
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
            'user' => $this->user,
            'user_id' => $this->user_id,
            'date' => Carbon::parse($this->login)->toDateString(),
            'login' => $this->login ? Carbon::parse($this->login)->toTimeString() : null,
            'logout' => $this->logout ? Carbon::parse($this->logout)->toTimeString() : null,
            'login_location' => $this->login_location,
            'logout_location' => $this->logout_location,
            'login_location_latitude' => json_decode($this->login_location)->latitude,
            'login_location_longitude' => json_decode($this->login_location)->longitude,
        ];
    }
}
