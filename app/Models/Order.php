<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order', 'type', 'area_id', 'address', 'content',
        'user_id', 'repair_id', 'assess', 'assess_content'
    ];
}
