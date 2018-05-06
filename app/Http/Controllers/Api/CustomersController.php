<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\Plam\CustomerRequest;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $customer = User::whereIdentify(2)
            ->with('school')
            ->paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return UserResource::collection($customer);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function store(CustomerRequest $customerRequest, User $user)
    {
        $customer = $user->create([
            'name'      => $customerRequest->name,
            'email'     => $customerRequest->email,
            'password'  => bcrypt($customerRequest->password),
            'identify'  => 2, // 客户
            'school_id' => $customerRequest->school_id,
            'status'    => $customerRequest->status
        ]);

        return new CustomerResource($customer);
    }

    public function update(Request $request, User $user)
    {
        $user->fill($request->all());
        if (!empty($user->password)) {
            $user->password = bcrypt($user->password);
        }
        $user->save();

        return response([
            'code' => 0,
            'msg'  => '更新成功'
        ]);
    }

    public function weappUserUpdate(Request $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return response(['error' => '验证码失效'], 400);
        }

        if (!hash_equals($verifyData['code'], $request->phone_code)) {
            // 返回401
            return response(['error' => '验证码错误'], 401);
        }

        $code = $request->code;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return response(['error' => 'code 无效'], 401);
        }

        $info = [
            'name'       => $request->name,
            'sex'        => $request->sex,
            'avatar'     => $request->avatar,
            'address'    => $request->address,
            'school_id'  => $request->school_id,
            'updated_at' => now()->toDateTimeString()
        ];

        if (!empty($request->phone)) {
            // 验证规则
            $rules = [
                'phone' => [
                    'regex:/^1[3456789]\d{9}$/',
                    'unique:users'
                ]
            ];

            $messages = [
                'phone.regex'  => '手机格式不正确',
                'phone.unique' => '手机号码已被使用',
            ];

            // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
            $params = $this->validate($request, $rules, $messages);
            $info['phone'] = $params['phone'];
        }

        // 找到 openid 对应的用户
        User::whereOpenid($data['openid'])->update($info);

        return new UserResource(User::whereOpenid($data['openid'])->with('school')->first());
    }
}
