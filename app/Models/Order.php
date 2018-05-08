<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order', 'type', 'area_id', 'address', 'content', 'school_id',
        'user_id', 'repair_id', 'assess', 'assess_content'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id')
            ->select('id', 'name');
    }

    public function images()
    {
        return $this->hasMany(OrderImages::class, 'order_id', 'id')
            ->select('id', 'image_url', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->select('id', 'name', 'sex', 'avatar', 'address');
    }

    public function processes()
    {
        return $this->hasMany(OrderProcess::class, 'order_id', 'id')
            ->select('id', 'type', 'order_id');
    }
}
