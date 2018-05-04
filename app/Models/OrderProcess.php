<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProcess extends Model
{
    protected $fillable = [
        'type', 'content', 'order_id'
    ];
}
