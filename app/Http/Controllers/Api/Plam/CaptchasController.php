<?php

namespace App\Http\Controllers\Api\Plam;

use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;

class CaptchasController extends Controller
{
    public function store(Request $request, CaptchaBuilder $captchaBuilder)
    {
        // 已存在验证码 先清空缓存
        if ($request->captcha_key !== 'captcha-no') {
            cache()->forget($request->captcha_key);
        }
        $key = 'captcha-' . str_random(15);
        $captcha = $captchaBuilder->build();
        $expiredAt = now()->addMinutes(10);
        cache()->put($key, ['code' => $captcha->getPhrase()], $expiredAt);

        return response([
            'captcha_key'           => $key,
            'expired_at'            => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ]);

    }
}
