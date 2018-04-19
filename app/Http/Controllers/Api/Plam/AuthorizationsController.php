<?php

namespace App\Http\Controllers\Api\Plam;

use App\Http\Resources\UserResource;
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

    public function logout()
    {
        \Auth::guard('api')->logout();

        return response()->json([
            'code' => 0,
            'msg'  => '退出成功'
        ]);
    }
}
