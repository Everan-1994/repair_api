<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\Evaluate;
use App\Models\Statement;
use App\Models\OrderImages;
use App\Models\OrderProcess;
use Illuminate\Http\Request;
use App\Jobs\FixedOrderMessage;
use App\Notifications\OrderNotify;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Api\OrderRequest;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user_id = \Auth::id();

        $order = Order::whereSchoolId($request->school_id)
            ->with(['area', 'images', 'user', 'repair'])
            ->when($request->self > 0, function ($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            })
            ->when(isset($request->status), function ($query) use ($request) {
                if ($request->self > 0) {
                    switch ($request->status) {
                        case 1:
                            // 3 已完成 && 5 已评价
                            // return $query->where('status', 3)->orWhere('status', 5);
                            return $query->whereRaw('(status = ? OR status = ?)', [3, 5]);
                            break;
                        case 2:
                            // 4 申述中
                            return $query->whereStatus(4);
                            break;
                        default:
                            // 0 申报中 && 1 驳回 && 2 维修中
                            return $query->whereBetween('status', [0, 2]);
                            break;
                    }
                } else {
                    return $query->whereStatus($request->status);
                }
            })
            ->when(!is_null($request->type), function ($query) use ($request) {
                return $query->whereType($request->type);
            })
            ->orderBy($request->created_at ?: 'created_at', $request->desc ?: 'desc')
            ->paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return OrderResource::collection($order);
    }

    public function store(OrderRequest $orderRequest, Order $order)
    {
        // 生成15位唯一订单号
        $order_sn = createOrderNm();

        $od = $order->create([
            'order'     => $order_sn,
            'school_id' => $orderRequest->school_id,
            'area_id'   => $orderRequest->area_id,
            'type'      => $orderRequest->type,
            'address'   => $orderRequest->address,
            'content'   => $orderRequest->contents,
            'user_id'   => \Auth::id(),
            'status'    => 0,
            'form_id'   => $orderRequest->form_id
        ]);

        if (!empty($orderRequest->imagesUrl)) {
            foreach ($orderRequest->imagesUrl as $val) {
                $arr[] = [
                    'order_id'   => $od['id'],
                    'image_url'  => $val,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString()
                ];
            }
            OrderImages::insert($arr);
        }

        // 通知管理员有新工单
        $ods = $order->whereId($od['id'])->first();
        $ods->types = 5;
        dd($ods->toArray());
        $user = User::where(['school_id' => $od['school_id'], 'identify' => 2])->first();
        $user->notify(new OrderNotify($ods));

        return response([
            'code' => 0,
            'msg'  => 'success'
        ]);

        \DB::beginTransaction();
        try {
            $od = $order->create([
                'order'     => $order_sn,
                'school_id' => $orderRequest->school_id,
                'area_id'   => $orderRequest->area_id,
                'type'      => $orderRequest->type,
                'address'   => $orderRequest->address,
                'content'   => $orderRequest->contents,
                'user_id'   => \Auth::id(),
                'status'    => 0,
                'form_id'   => $orderRequest->form_id
            ]);

            if (!empty($orderRequest->imagesUrl)) {
                foreach ($orderRequest->imagesUrl as $val) {
                    $arr[] = [
                        'order_id'   => $od['id'],
                        'image_url'  => $val,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString()
                    ];
                }
                OrderImages::insert($arr);
            }

            \DB::commit();

            // 通知管理员有新工单
            $ods = $order->whereId($od['id'])->first();
            $ods->types = 5;
            $user = User::where(['school_id' => $od['school_id'], 'identify' => 2])->first();
            $user->notify(new OrderNotify($ods));

            return response([
                'code' => 0,
                'msg'  => 'success'
            ]);
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['error' => $exception->getMessage()], $exception->getCode());
        }

    }

    public function update(OrderRequest $orderRequest, Order $order, OrderProcess $orderProcess)
    {
        \DB::beginTransaction();
        try {
            $order->whereId($order['id'])->update([
                'area_id'    => $orderRequest->area_id,
                'type'       => $orderRequest->type,
                'address'    => $orderRequest->address,
                'content'    => $orderRequest->contents,
                'status'     => 0,
                'updated_at' => now()->toDateTimeString()
            ]);

            if ($order['status'] == 1) {
                // 新增进度
                $orderProcess->create([
                    'type'     => 0,
                    'user_id'  => $order['user_id'], // 申报人
                    'order_id' => $order['id'],
                    'content'  => $orderRequest->contents
                ]);
            }

            $images = OrderImages::where('order_id', $order['id'])->exists();

            // 删除图片
            if ($images && (count($orderRequest->imagesUrl) == 3 || empty($orderRequest->oldImages))) {
                OrderImages::where('order_id', $order['id'])->delete();

//                $common = new CommonsController();
//                // 云服务器删除图片
//                foreach ($images as $image) {
//                    $common->delImage($image);
//                }
            }

            if (!empty($orderRequest->imagesUrl)) {
                foreach ($orderRequest->imagesUrl as $val) {
                    $arr[] = [
                        'order_id'   => $order['id'],
                        'image_url'  => $val,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString()
                    ];
                }
                OrderImages::insert($arr);
            }

            \DB::commit();

            return response([
                'code' => 0,
                'msg'  => 'success'
            ]);
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function getAllOrder(Request $request, Order $order)
    {
        $list = $order->whereSchoolId($request->school_id)
            ->paginate(10, ['*'], 'page', $request->page ?: 1);

        return OrderResource::collection($list);
    }

    public function show(Order $order)
    {
        return new OrderResource($order->whereId($order['id'])->with(['user', 'repair', 'images', 'area', 'processes', 'evaluate'])->first());
    }

    public function del(Order $order)
    {
        $this->authorize('destroy', $order);
        $order->delete();

        return response([
            'code' => 0,
            'msg'  => 'Successed'
        ], 204);
    }

    /**
     * 申报回复(驳回) or 已完成维修
     */
    public function replies(Request $request)
    {
        \DB::beginTransaction();
        try {
            if ($request->order_status == 3) {
                $reply = OrderProcess::create(
                    [
                        'order_id' => $request->order_id,
                        'user_id'  => \Auth::id(),
                        'type'     => $request->type,
                        'content'  => $request->content
                    ]
                );
                // 更新工单状态(已完成)
                Order::whereId($request->order_id)->update(['status' => 3]);
            } else {
                $reply = OrderProcess::updateOrCreate(
                    [
                        'order_id' => $request->order_id,
                        'user_id'  => \Auth::id(),
                        'type'     => $request->type
                    ],
                    [
                        'content' => $request->content
                    ]
                );
                // 更新工单状态(驳回)
                Order::whereId($request->order_id)->update(['status' => 1]);
            }

            \DB::commit();

            $od = Order::whereId($request->order_id)->first();

            if ($request->order_status == 3) {
                // 通知用户工单已完成
                $od->types = 3;
                $od->user->notify(new OrderNotify($od));
            } else {
                // 通知用户工单被驳回
                $od->types = 0;
                $od->user->notify(new OrderNotify($od));
            }

        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['error' => $exception->getMessage()], 500);
        }

        return response($reply, 200);
    }

    /**
     * 维修员维修的工单
     */
    public function getOrderList(Request $request)
    {
        $user_id = \Auth::id();

        $order = Order::whereSchoolId($request->school_id)
            ->with(['area', 'images', 'user'])
            ->when(isset($request->status), function ($query) use ($request) {
                switch ($request->status) {
                    case 1:
                        // 3 已完成
                        return $query->whereStatus(3);
                        break;
                    case 2:
                        // 5 已评价
                        return $query->whereStatus(5);
                        break;
                    default:
                        // 0 维修中
                        return $query->whereStatus(2);
                        break;
                }
            })
            ->when($request->self == 1, function ($query) use ($user_id) {
                return $query->whereRepairId($user_id);
            })
            ->orderBy($request->created_at ?: 'created_at', $request->desc ?: 'desc')
            ->paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return OrderResource::collection($order);
    }

    public function dispatchs(Request $request, Order $order, OrderProcess $orderProcess, MessageController $message)
    {
        \DB::beginTransaction();
        try {
            if ($request->order_status == 2) {
                // 新增进度
                $orderProcess->create(
                    [
                        'type'     => 2,
                        'order_id' => $request->order_id,
                        'content'  => '工单已受理。',
                        'user_id'  => $request->repair_id, // 维修员id
                    ]
                );
            } else {
                // 新增进度 & 更新
                $orderProcess->updateOrCreate(
                    [
                        'type'     => 2,
                        'order_id' => $request->order_id,
                        'content'  => '工单已受理。'
                    ],
                    [
                        'user_id' => $request->repair_id, // 维修员id
                    ]
                );
            }

            // 更新工单
            $order->whereId($request->order_id)->update([
                'status'     => 2,
                'repair_id'  => $request->repair_id,
                'updated_at' => now()->toDateTimeString()
            ]);

            $od = $order->whereId($request->order_id)->first();

            if ($od->status == 0) {
                // 通知用户工单已派工
                $od->types = 2;
                $od->user->notify(new OrderNotify($od));
            }

            // 通知维修员有新工单
            $od->types = 1;
            $od->repair->notify(new OrderNotify($od));

            // 模板消息提醒(队列)
            // $msg = dispatch(new NewOrderMessage($od));

            \DB::commit();

            return response([
                'code' => 0,
                'msg'  => 'Success'
            ], 201);

        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['error' => $exception->getMessage()], 500);
        }

    }

    public function fixedOrder(Request $request, Order $order, OrderProcess $orderProcess)
    {
        \DB::beginTransaction();
        try {
            // 新增进度
            $orderProcess->create([
                'type'     => 3,
                'user_id'  => \Auth::id(), // 维修员id
                'order_id' => $request->order_id,
                'content'  => $request->content ?: '工单已完成。',
            ]);

            // 更新工单
            $order->whereId($request->order_id)->update([
                'status'         => 3,
                'repair_form_id' => $request->form_id, // 完成工单使用
                'updated_at'     => now()->toDateTimeString()
            ]);

            \DB::commit();

            $od = $order->whereId($request->order_id)->first();
            // 通知用户工单已完成
            $od->types = 3;
            $od->user->notify(new OrderNotify($od));

            // 完成工单提醒 模板消息
            // dispatch(new FixedOrderMessage($od));
            $message = new MessageController();
            $message->fixedOrderMessage($od->id);

            return response([
                'code' => 0,
                'msg'  => 'success'
            ], 201);
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['error' => $exception->getMessage()], 500);
        }
    }

    public function evaluateOrder(Request $request, Order $order, OrderProcess $orderProcess, Evaluate $evaluate)
    {
        \DB::beginTransaction();
        try {
            // 新增进度
            $op = $orderProcess->create([
                'type'     => 5,
                'user_id'  => \Auth::id(), // 用户id
                'order_id' => $request->order_id,
                'content'  => $request->content,
            ]);

            // 更新工单
            $order->whereId($request->order_id)->update([
                'status'     => 5,
                'updated_at' => now()->toDateTimeString()
            ]);

            // 评价
            $evaluate->create([
                'order_id'   => $request->order_id,
                'ps_id'      => $op['id'],
                'content'    => $request->content,
                'evaluate'   => $request->evaluate,
                'service'    => $request->sstar,
                'efficiency' => $request->estar
            ]);

            \DB::commit();

            $od = $order->whereId($request->order_id)->first();
            // 通知维修员工单已评价
            $od->types = 4;
            $od->repair->notify(new OrderNotify($od));

            // 评价 模板消息发送
            $message = new MessageController();
            $message->evaluateOrderMessage($od->id);

            return response([
                'code' => 0,
                'msg'  => 'success'
            ], 201);
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['error' => $exception->getMessage()], 500);
        }
    }

    public function statementOrder(Request $request, Order $order, OrderProcess $orderProcess, Statement $statement)
    {
        \DB::beginTransaction();
        try {
            // 新增进度
            $op = $orderProcess->create([
                'type'     => 4,
                'user_id'  => \Auth::id(), // 用户id
                'order_id' => $request->order_id,
                'content'  => $request->content,
            ]);

            // 更新工单
            $order->whereId($request->order_id)->update([
                'status'     => 4,
                'updated_at' => now()->toDateTimeString()
            ]);

            // 申述图片
            if (!empty($request->imagesUrl)) {
                foreach ($request->imagesUrl as $val) {
                    $arr[] = [
                        'ps_id'      => $op['id'],
                        'image_url'  => $val,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString()
                    ];
                }
                $statement->insert($arr);
            }

            \DB::commit();

            return response([
                'code' => 0,
                'msg'  => 'success'
            ], 201);
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * 今日新工单 & 今日已完成工单 & 工单总数
     */
    public function getOrderCount(Request $request, Order $order)
    {
        $type = $request->type ?: 0;
        $today = now()->toDateString();

        $count = $order->where('school_id', $request->school_id)
            ->when($type > 0, function ($query) use ($type, $today) {
                switch ($type) {
                    case 1:
                        // 今日新工单
                        return $query->whereStatus(0)->whereDate('created_at', $today);
                        break;
                    case 2:
                        // 今日已完成工单
                        return $query->whereRaw('(status = ? OR status = ?)', [3, 5])
                            ->whereDate('created_at', $today);
                        break;
                }
            })
            ->count();

        return response(['count' => $count]);
    }

    public function getRepairEvaluate(Request $request, Order $order)
    {
        $list = $order->where('school_id', $request->school_id)->with('evaluate')->get()->toArray();

        $data = [
            'hp' => 0,
            'zp' => 0,
            'cp' => 0
        ];

        foreach ($list as $k => $eva) {
            if (!is_null($eva['evaluate'])) {
                switch ($eva['evaluate']['evaluate']) {
                    case '好评':
                        $data['hp']++;
                        break;
                    case '中评':
                        $data['zp']++;
                        break;
                    case '差评':
                        $data['cp']++;
                        break;
                }
            }
        }

        return response($data);
    }

    /**
     * 最近七天申报量
     */
    public function getWeekOrder(Request $request)
    {
        $list = \DB::select("
                    select
                      DATE_FORMAT(a.created_at, '%m-%d') as days,
                      ifnull(b.count,0) as count
                    from (
                           SELECT date_sub(curdate(), interval 1 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 2 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 3 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 4 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 5 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 6 day) as created_at
                           union all
                           SELECT date_sub(curdate(), interval 7 day) as created_at
                         ) a left join (
                                         select date(created_at) as datetime, count(*) as count
                                         from orders
                                         where school_id = ?
                                         group by date(created_at)
                                       ) b on a.created_at = b.datetime
                                       order by days desc
                ", [$request->school_id]);

        return response($list);
    }

    /**
     * 每月申报量
     */
    public function getMonthOrder(Request $request, Order $order)
    {
        $list = $order->where('school_id', $request->school_id)
            ->select(\DB::raw("DATE_FORMAT(created_at,'%Y-%m') as times, COUNT(*) as count"))
            ->groupBy('times')
            ->get()
            ->toArray();

        // 构造12个月 YYYY-MM
        for ($i = 0; $i <= 11; $i++) {
            $month = now()->modify('-' . $i . ' months')->toDateString();
            $ms[$i] = substr($month, 0, 7);
        }

        $ms = array_reverse($ms); // 倒序排列

        foreach ($ms as $c => $w) {
            foreach ($list as $k => $v) {
                if (hash_equals($w, $v['times'])) {
                    $data[$c][$k] = $v;
                } else {
                    $data[$c][$k] = [
                        'times' => $w,
                        'count' => 0
                    ];
                }
            }
        }

        foreach ($data as $k => $v) {
            $sum[$k] = [];
            foreach ($v as $c => $w) {
                array_push($sum[$k], $w['count']);
            }

            $date[$k] = [
                'day'   => $v[0]['times'],
                'count' => array_sum($sum[$k])
            ];
        }

        return response($date);
    }
}
