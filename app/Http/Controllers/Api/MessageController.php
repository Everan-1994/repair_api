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

        if (!$order->form_id && $order->form_id !== 'the formId is a mock one') {
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
                    'keyword5' => intval($total_time / 60) . '分钟' // 取整
                ],
            ]);
        }

    }

    /**
     * 工单评价提醒
     */
    public function evaluateOrderMessage($id)
    {
        $order = Order::whereId($id)->with('processes')->first();

        if (!$order->repair_form_id && $order->repair_form_id !== 'the formId is a mock one') {
            foreach ($order->processes as $process) {
                if ($process['type'] == 5) {
                    $evaluate = $process['evaluate'];
                    $service = $process['service'];
                    $efficiency = $process['efficiency'];
                    $content = $process['content'];
                }
            }

            $this->app->template_message->send([
                'touser'      => $order->repair->openid,
                'template_id' => 'tYB2lN_ZYlbVwthCbF43EZzOyRX2kBwmqmX5bNMQQik',
                'page'        => 'pages/show?id=' . $order->id,
                'form_id'     => $order->repair_form_id,
                'data'        => [
                    'keyword1' => $order->order,
                    'keyword2' => $content,
                    'keyword3' => $evaluate,
                    'keyword4' => $service . '颗星',
                    'keyword5' => $efficiency . '颗星'
                ],
            ]);
        }
    }
}
