<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluate extends Model
{
    protected $fillable = [
        'order_id', 'evaluate', 'content', 'service', 'efficiency'
    ];
}
