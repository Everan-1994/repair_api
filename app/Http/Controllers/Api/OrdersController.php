<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderImages;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Api\OrderRequest;

class OrdersController extends Controller
{
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
}
