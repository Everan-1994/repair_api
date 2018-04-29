<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    public function login(Request $request)
    {
        if (app()->environment('production')) {
            $verifyData = \Cache::get($request->captcha_key);

            if (!$verifyData) {
                return response(['error' => '无效的验证码'], 422);
            }

            if (!hash_equals($verifyData['code'], $request->captcha)) {
                return response(['error' => '验证码不匹配'], 400);
            }
        }

        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if (!$this->checkStatus($credentials)) {
            return response(['error' => '账号已被冻结，请联系管理员。'], 400);
        }

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            return response(['error' => '账号或密码错误'], 400);
        }

        // 记录登入日志
        // event(new LoginEvent(\Auth::guard('api')->user(), new Agent(), $request->getClientIp()));


        // 使用 Auth 登录用户
        return (new UserResource(\Auth::guard('api')->user()))->additional(['meta' => [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => \Auth::guard('api')->factory()->getTTL() * 60
        ]]);
    }

    // 小程序登录
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return response(['error' => 'code 无效'], 401);
        }

        // 找到 openid 对应的用户 找不到则创建
        $user = User::whereOpenid($data['openid'])->first();

        if (!$user) {
            $user = User::create([
                'name'     => $request->nickname,
                'sex'      => $request->sex,
                'avatar'   => $request->avatar,
                'status'   => 1,
                'password' => bcrypt('Everan9457'),
                'openid'   => $data['openid'],
            ]);
        }

        $attributes['weixin_session_key'] = $data['session_key'];

        if ($user['status'] !== 1) {
            return response(['error' => '账号已被冻结，请联系管理员。'], 400);
        }

        // 更新用户数据
        $user->update($attributes);

        // 记录登入日志
        // event(new LoginEvent(\Auth::guard('api')->user(), new Agent(), $request->getClientIp()));

        return (new UserResource($user))->additional(['meta' => [
            'access_token' => 'Bearer ' . \Auth::guard('api')->fromUser($user),
            'expires_in'   => \Auth::guard('api')->factory()->getTTL() * 60
        ]]);
    }

    public function logout()
    {
        \Auth::guard('api')->logout();

        return response()->json([
            'code' => 0,
            'msg'  => '退出成功'
        ]);
    }

    public function checkStatus($map)
    {
        $map['status'] = 1;

        return \Auth::attempt($map);
    }
}
