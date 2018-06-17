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
            'address'        => $this->address,
            'content'        => $this->content,
            'user'           => $this->whenLoaded('user'),
            'repair'         => $this->whenLoaded('repair'),
            'images'         => $this->whenLoaded('images'),
            'area'           => $this->whenLoaded('area'),
            'processes'      => $this->whenLoaded('processes'),
            'evaluate'       => $this->whenLoaded('evaluate'),
            'repair_id'      => $this->repair_id,
            'status'         => $this->status,
            'created_at'     => $this->created_at->toDateTimeString(),
            'updated_at'     => $this->updated_at->toDateTimeString(),
        ];
    }
}
