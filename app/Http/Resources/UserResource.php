<?php

namespace App\Http\Resources;

class UserResource extends Resource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone ? true : false,
            'avatar' => $this->avatar,
            'identify' => $this->identify
        ];
    }
}
