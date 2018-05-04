<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderImages extends Model
{
    protected $fillable = [
        'order_id', 'url'
    ];
}
