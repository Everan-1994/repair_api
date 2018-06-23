<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderNotify extends Notification
{
    use Queueable;
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        // return ['mail'];
        return ['database']; // 通知频道
    }

    public function toDatabase($notifiable)
    {
        // 存入数据库data字段里的数据
        return [
            'type'      => $this->order->types,
            'order_id'  => $this->order->id,
            'order'     => $this->order->order,
            'content'   => $this->order->content,
            'name'      => $this->order->repair->truename ?: $this->order->user->name,
            'avatar'    => $this->order->repair->avatar ?: $this->order->user->avatar,
            'status'    => $this->order->status,
            'image_rul' => $this->order->images
        ];
    }
}