<?php

namespace App\Http\Resources;


class OrderResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'order'          => $this->order,
            'type'           => $this->types,
            'school_id'      => $this->school_id,
            'area'           => $this->whenLoaded('area'),
            'address'        => $this->address,
            'content'        => $this->content,
            'user'           => $this->whenLoaded('user'),
            'repair'         => $this->whenLoaded('repair'),
            'images'         => $this->whenLoaded('images'),
            'processes'      => $this->whenLoaded('processes'),
            'repair_id'      => $this->repair_id,
            'access'         => $this->access,
            'access_content' => $this->access_content,
            'status'         => $this->status,
            'created_at'     => $this->created_at->toDateTimeString(),
            'updated_at'     => $this->updated_at->toDateTimeString(),
        ];
    }
}
