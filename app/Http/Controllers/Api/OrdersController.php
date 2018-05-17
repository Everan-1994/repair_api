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
            ->with(['area', 'images', 'user'])
            ->when(isset($request->status), function ($query) use ($request) {
                return $query->whereStatus($request->status);
            })
            ->when($request->self == 1, function ($query) use ($user_id) {
                return $query->whereUserId($user_id);
            })
            ->when(!is_null($request->type), function ($query) use ($request) {
                return $query->whereType($request->type);
            })
            ->orderBy($request->created_at ?: 'created_at', $request->desc ?: 'desc')
            ->paginate($request->pageSize ?: 5, ['*'], 'page', $request->page ?: 1);

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
                'status'    => $orderRequest->status
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
            return response(['error' => $exception->getMessage()], $exception->getCode());
            DB::rollBack();
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
        ], 200);
    }

    /**
     * 申报回复(驳回)
     */
    public function replies(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = \Auth::id();
        dd($data);
//        \DB::beginTransaction();
//        try {
//            $reply = OrderProcess::where($data)->firstOrCreate();
//            // 更新工单状态(驳回)
//            Order::whereId($data['order_id'])->update(['status', 1]);
//            \DB::commit();
//        } catch (\Exception $exception) {
//            DB::rollBack();
//            return response(['error' => '系统出错'], 500);
//        }


        // return response($reply, 200);
    }
}
