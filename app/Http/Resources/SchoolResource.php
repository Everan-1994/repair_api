<?php

namespace App\Http\Resources;

class SchoolResource extends Resource
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
            'id'          => $this->id,
            'logo'        => $this->logo,
            'school_name' => $this->school_name,
            'school_code' => $this->school_code,
            'status'      => $this->status,
            'bind'        => $this->bind == 1 ? '已绑定' : '未绑定',
        ];
    }
}
