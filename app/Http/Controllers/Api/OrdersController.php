<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderImages;
use App\Models\OrderProcess;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Api\OrderRequest;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user_id = \Auth::id();

        $order = Order::whereSchoolId($request->school_id)
            ->with(['area', 'images', 'user', 'repair'])
            ->when(isset($request->status), function ($query) use ($request) {
                if ($request->self == 1) {
                    switch ($request->status) {
                        case 1:
                            // 0 申述中
                            return $query->whereStatus(4);
                            break;
                        case 2:
                            // 3 已完成 && 5 已评价
                            return $query->whereStatus(3)->orWhere('status', 5);
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
            ->when($request->self == 1, function ($query) use ($user_id) {
                return $query->whereUserId($user_id);
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

        \DB::beginTransaction();
        try {
            $order = $order->create([
                'order'     => $order_sn,
                'school_id' => $orderRequest->school_id,
                'area_id'   => $orderRequest->area_id,
                'type'      => $orderRequest->type,
                'address'   => $orderRequest->address,
                'content'   => $orderRequest->contents,
                'user_id'   => \Auth::id(),
                'status'    => $orderRequest->status ?: 0
            ]);

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

            return response($order);
        } catch (\Exception $exception) {
            \DB::rollBack();
            return response(['error' => $exception->getMessage()], $exception->getCode());
        }

    }

    public function update(OrderRequest $orderRequest, Order $order)
    {
        \DB::beginTransaction();
        try {
            $order->whereId($order['id'])->update([
                'area_id'    => $orderRequest->area_id,
                'type'       => $orderRequest->type,
                'address'    => $orderRequest->address,
                'content'    => $orderRequest->contents,
                'updated_at' => now()->toDateTimeString()
            ]);

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
        return new OrderResource($order->whereId($order['id'])->with(['user', 'repair', 'images', 'area', 'processes'])->first());
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
     * 申报回复(驳回)
     */
    public function replies(Request $request)
    {
        \DB::beginTransaction();
        try {
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
            \DB::commit();
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
                        // 0 申报中 && 2 维修中
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

    public function dispatchs(Request $request, Order $order, OrderProcess $orderProcess)
    {
        \DB::beginTransaction();
        try {
            // 新增进度
            $orderProcess->create([
                'type'     => 2,
                'user_id'  => $request->repair_id, // 维修中
                'order_id' => $request->order_id,
                'content'  => '工单已受理。'
            ]);

            // 更新工单
            $order->whereId($request->order_id)->update([
                'status'     => 2,
                'repair_id'  => $request->repair_id,
                'updated_at' => now()->toDateTimeString()
            ]);
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
                'status'     => 3,
                'updated_at' => now()->toDateTimeString()
            ]);
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
}
