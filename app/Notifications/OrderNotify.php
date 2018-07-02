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
        if (in_array($this->order->types, [0, 2, 3, 5])) {
            $bool = true;
        } else {
            $bool = false;
        }
        dd($this->order);
        // 存入数据库data字段里的数据
        return [
            'type'      => $this->order->types,
            'order_id'  => $this->order->id,
            'order'     => $this->order->order,
            'content'   => $this->order->content,
            'name'      => $bool ? $this->order->user->name : $this->order->repair->truename,
            'avatar'    => $bool ? $this->order->user->avatar : $this->order->repair->avatar,
            'status'    => $this->order->status
        ];
    }
}
