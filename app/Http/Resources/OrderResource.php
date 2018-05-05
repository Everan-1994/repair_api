<?php

namespace App\Http\Resources;


class OrderResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'order'          => $this->order,
            'type'           => $this->type,
            'school_id'      => $this->school_id,
            'area'           => $this->whenLoaded('area'),
            'address'        => $this->address,
            'content'        => $this->content,
            'user'           => $this->whenLoaded('user'),
            'images'         => $this->whenLoaded('images'),
            'repair_id'      => $this->repair_id,
            'access'         => $this->access,
            'access_content' => $this->access_content,
            'status'         => $this->status,
            'created_at'     => $this->created_at->toDateTimeString()
        ];
    }
}
