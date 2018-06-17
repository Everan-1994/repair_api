<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProcess extends Model
{
    protected $fillable = [
        'type', 'content', 'order_id', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->select('id', 'name', 'avatar', 'truename', 'phone');
    }

    /**
     * 评价
     */
    public function evaluate()
    {
        return $this->hasOne(Evaluate::class, 'ps_id', 'id');
    }
}
