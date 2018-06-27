<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use EasyWeChat\Factory;

class MessageController extends Controller
{
    protected $app;

    public function __construct()
    {
        $this->app = Factory::miniProgram([
            'app_id'        => env('WECHAT_MINI_PROGRAM_APPID'),
            'secret'        => env('WECHAT_MINI_PROGRAM_SECRET'),

            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array'
        ]);
    }

    /**
     * 新工单提醒
     */
    public function newOrderMessage($id)
    {
        $order = Order::whereId($id)->first();

        $this->app->template_message->send([
            'touser'      => $order->user->openid,
            'template_id' => 's1dJ2Tirds-kqLD4PGmfzHBEzJASinF8Gsn6bbgyZCU',
            'page'        => 'pages/show?id=' . $order->id,
            'form_id'     => $order->form_id,
            'data'        => [
                'keyword1' => $order->order,
                'keyword2' => '新工单',
                'keyword3' => $order->user->name,
                'keyword4' => $order->content,
                'keyword5' => $order->created_at->toDateTimeString()
            ],
        ]);
    }

    /**
     * 工单完成提醒
     */
    public function fixedOrderMessage($id)
    {
        $order = Order::whereId($id)->with('processes')->first();

        foreach ($order->processes as $process) {
            if ($process['type'] == 3) {
                $end_time = $process['created_at'];
            }
        }

        $total_time = strtotime($end_time) - strtotime($order->created_at->toDateTimeString());

        $this->app->template_message->send([
            'touser'      => $order->user->openid,
            'template_id' => 'cgh4HpnxhQHWXzEL5sgoUOrLN6URPm1h0OIrfPS1qnI',
            'page'        => 'pages/show?id=' . $order->id,
            'form_id'     => $order->form_id,
            'data'        => [
                'keyword1' => $order->order,
                'keyword2' => '工单已完成',
                'keyword3' => $order->content,
                'keyword4' => $order->created_at->toDateTimeString(),
                'keyword5' => intval($total_time / 60) . '分' // 取整
            ],
        ]);
    }
}
