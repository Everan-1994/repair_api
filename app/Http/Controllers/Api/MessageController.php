<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use EasyWeChat\Factory;

class MessageController extends Controller
{
    protected $app;
    protected $order;
    public function __construct(Order $order)
    {
        $this->order = $order;

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
    public function newOrderMessage()
    {
        return $this->app->template_message->send([
            'touser'      => $this->order->repair->openid,
            'template_id' => 's1dJ2Tirds-kqLD4PGmfzHBEzJASinF8Gsn6bbgyZCU',
            'page'        => 'pages/show?id=' . $this->order->id,
            'form_id'     => $this->order->form_id,
            'data'        => [
                'keyword1' => $this->order->order,
                'keyword2' => '新工单',
                'keyword3' => $this->order->user->name,
                'keyword4' => $this->order->content,
                'keyword5' => $this->order->created_at->toDateTimeString()
            ],
        ]);
    }

    /**
     * 工单完成提醒
     */
    public function fixedOrderMessage($order)
    {

    }
}
