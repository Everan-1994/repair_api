<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    public function info()
    {
        $user = User::whereId(\Auth::guard('api')->id())->with('school')->first();

        return new UserResource($user);
    }

    // 修改密码
    public function changePwd(Request $request)
    {
        $user = User::whereId(\Auth::guard('api')->id())->first();
        $user->password = bcrypt($request->new_pwd);
        $user->save();

        return response([
            'code' => 0,
            'msg' => 'Success'
        ]);
    }

    /**
     * 用户总数
     */
    public function getUserCount(Request $request)
    {
        $count = User::where('school_id', $request->school_id)->count();

        return response(['count' => $count]);
    }

    /**
     * 维修员工单
     */
    public function getUserOrderCount(Request $request)
    {
        $list = User::where([
                'school_id' => $request->school_id,
                'identify' => 4
            ])
            ->with('orders')
            ->get()
            ->toArray();

        if ($list) {
            foreach ($list as $k => $v) {
                $evaluate[$k] = 0;
                foreach ($v['orders'] as $c => $w) {
                    is_null($w['evaluate']) ?: $evaluate[$k]++;
                }

                $data[$k] = [
                    'name' => $v['truename'],
                    'order_count' => count($v['orders']),
                    'evaluate' => $evaluate[$k]
                ];
            }
        } else {
            $data = [];
        }

        return response($data);
    }
}
