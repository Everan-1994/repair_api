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
            'name'      => $this->name,
            'email'     => $this->email,
            'school_id' => $this->school_id,
            'phone'     => $this->phone ? true : false,
            'avatar'    => $this->avatar,
            'identify'  => $this->identify,
            'status'    => $this->status
        ];
    }
}
