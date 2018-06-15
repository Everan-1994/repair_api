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

    /**
     * 申报者信息
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->select('id', 'name', 'sex', 'avatar', 'address');
    }

    /**
     * 维修员信息
     */
    public function repair()
    {
        return $this->belongsTo(User::class, 'repair_id', 'id')
            ->select('id', 'name', 'truename', 'sex', 'avatar');
    }

    public function processes()
    {
        return $this->hasMany(OrderProcess::class, 'order_id', 'id')
            ->with('user')
            ->orderBy('id', 'asc');
    }

    /**
     * 申报类型
     */
    public function types()
    {
        return $this->belongsTo(Type::class, 'type', 'id');
    }
}
