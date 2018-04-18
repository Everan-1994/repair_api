<?php

namespace App\Http\Controllers\Api\Plam;

use Illuminate\Http\Request;

class AuthorizationsController extends Controller
{
    public function login(Request $request)
    {

        if (app()->environment('production')) {
            $verifyData = \Cache::get($request->captcha_key);

            if (!$verifyData) {
                $this->response->error('无效的验证码', 422);
            }

            if (!hash_equals($verifyData['code'], $request->captcha)) {

            }
        }

        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        if (!$token = \Auth::guard('api')->attempt($credentials)) {
            $this->response->errorUnauthorized('用户名或密码错误');
        }

        // 记录登入日志
        // event(new LoginEvent(\Auth::guard('api')->user(), new Agent(), $request->getClientIp()));
        

        // 使用 Auth 登录用户，如果登录成功，则返回 201 的 code 和 token，如果登录失败则返回
        return response([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => \Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
