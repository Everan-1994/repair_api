<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class MembersController extends Controller
{
    public function index(Request $request)
    {
        $user = User::whereSchoolId($request->school_id)
            ->when(isset($request->status), function ($query) use ($request) {
                return $query->whereStatus($request->status);
            })
            ->when(isset($request->truename), function ($query) use ($request) {
                return $query->where('truename', 'like', '%' . $request->truename . '%');
            })
            ->when(isset($request->phone), function ($query) use ($request) {
                return $query->where('phone', preg_replace('# #','',$request->phone));
            })
            ->whereIdentify($request->identify)
            ->orderBy($request->order ?: 'created_at', $request->sort ?: 'desc')
            ->paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return UserResource::collection($user);
    }

    /*
     * 变更身份为维修员
     */
    public function changeIdentify(Request $request)
    {
        if ($request->type == 1) {
            $hasOrder = Order::whereUserId($request->user_id)->exists(); // 存在申报
        } else {
            $hasOrder = Order::whereRepairId($request->user_id)->exists(); // 存在接单
        }

        if ($hasOrder) {
            return response([
                'code' => 1,
                'msg' => '该用户已存在工单关系，请先删除后再试。',
            ], 400);
        }

        User::whereId($request->user_id)->update([
            'identify' => $request->identify,
            'truename' => $request->repair_name,
        ]);

        return response([
            'code' => 0,
            'msg' => '更新成功',
        ]);
    }

    public function changeStatus(Request $request)
    {
        User::whereId($request->user_id)->update(['status' => $request->status]);

        return response([
            'code' => 0,
            'msg' => '更新成功',
        ]);
    }

    public function delUser(User $user)
    {
        $hasOrder = Order::whereUserId($user->id)->orWhere('repair_id', $user->id)->exists(); // 存在工单

        if ($hasOrder) {
            return response([
                'code' => 1,
                'msg' => '该用户已存在工单关系，请先删除后再试。',
            ], 400);
        }

        $user->delete();

        return response([
            'code' => 0,
            'msg' => '删除成功',
        ]);
    }
}
