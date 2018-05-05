<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderImages;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $order = Order::whereSchoolId($request->school_id)
            ->with(['area', 'images', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $request->page ?: 1);

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
}
