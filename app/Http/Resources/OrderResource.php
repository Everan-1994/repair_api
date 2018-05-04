<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id'             => $this->id,
            'order'          => $this->order,
            'type'           => $this->type,
            'area_id'        => $this->area_id,
            'address'        => $this->address,
            'content'        => $this->content,
            'user_id'        => $this->user_id,
            'repair_id'      => $this->repair_id,
            'assess'         => $this->assess,
            'assess_content' => $this->assess_content,
            'status'         => $this->status,
            'created_at'     => $this->created_at
        ];
    }
}
