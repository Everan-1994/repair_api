<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->data;

        return [
            'type'       => $data['type'],
            'order_id'   => $data['order_id'],
            'order'      => $data['order'],
            'content'    => $data['content'],
            'name'       => $data['name'],
            'avatar'     => $data['avatar'],
            'status'     => $data['status'],
            'created_at' => $this->created_at->toDateTimeString()
        ];

    }
}
