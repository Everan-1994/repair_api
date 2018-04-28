<?php

namespace App\Http\Resources;

class UserResource extends Resource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'school'    => new SchoolResource($this->whenLoaded('school')),
            'school_id' => $this->school_id,
            'phone'     => $this->when(!empty($this->phone), $this->phone),
            'avatar'    => $this->avatar,
            'identify'  => $this->identify,
            'status'    => $this->status
        ];
    }
}
