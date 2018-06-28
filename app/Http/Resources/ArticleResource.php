<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'content'    => $this->content,
            'status'     => $this->status,
            'view_count' => $this->view_count,
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
