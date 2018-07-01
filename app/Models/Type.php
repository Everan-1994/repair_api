<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'school_id', 'name'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'type', 'id');
    }
}
