<?php

namespace App\Http\Controllers\Api;

use Overtrue\EasySms\EasySms;
use App\Http\Requests\Request;
use App\Http\Requests\Api\VerificationCodeRequest;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = cache()->get($request->captcha_key);

        if (!$captchaData) {
            return response(['captchaError' => '验证码无效'], 400);
        }

        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清缓存
            cache()->forget($request->captcha_key);
            return response(['captchaError' => '验证码错误'], 401);
        }

        $phone = $captchaData['phone'];

        // 生成4位随机数字，左侧补0
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        // 本地或者测试环境，不必每次都真实发送验证码
        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            try {
                $easySms->send($phone, [
                    'template' => 'SMS_126462014',
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $exception) {
                $response = $exception->getResponse();
                $result = json_decode($response->getBody()->getContents(), true);
                throw new \Exception($result['msg'] ?: '短信发送异常');
            }
        }

        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinutes(5);

        cache()->put($key, ['phone' => $phone, 'code' => $code], $expiredAt);
        cache()->forget($request->captcha_key);

        return response([
            'key' => $key,
            'expiredAt' => $expiredAt->toDateTimeString()
        ]);
    }

    public function verifPhoneCode(Request $request)
    {
        $verifyData = cache()->get($request->verification_key);

        if (!$verifyData) {
            throw new \Exception('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            throw new \Exception('验证码错误', 401);
        }

        return response([
            'msg' => 'Success'
        ]);
    }
}
