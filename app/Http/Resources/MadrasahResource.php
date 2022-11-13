<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MadrasahResource extends JsonResource
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
            'name' => $this->name,
            'district' => $this->district,
            'address' => $this->address,
            'telephone' => $this->telephone,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'fax' => $this->fax,
            'principal_id' => $this->principal_id,
            'location' => $this->location,
            'latitude' => json_decode($this->location)->latitude ?? 23.774696,
            'longitude' => json_decode($this->location)->longitude ?? 290.384262,
        ];
    }
}
