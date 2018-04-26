<?php

namespace App\Http\Resources;

class CustomerResource extends Resource
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
            'name'    => $request->name,
            'email'   => $request->email,
            $this->mergeWhen($request->phone, [
                'phone' => $request->phone
            ]),
            'avatar'  => $request->avatar,
            'address' => $request->address
        ];
    }
}
