<?php

$api = app('Illuminate\Routing\Router');

// 后台API
$api->group([
    'namespace'  => 'Api\Plam',
], function ($api) {

    $api->group([
        'middleware' => 'throttle: 10, 1', // 调用接口限制 1分钟10次
    ], function ($api) {
        // 图片验证码
        $api->get('captchas/{captcha_key}', 'CaptchasController@store')
            ->name('api.captchas.store');
        // 登录
        $api->post('login', 'AuthorizationsController@login')
            ->name('api.authorizations.login');
    });

    $api->group([
        'middleware' => 'throttle: 60, 1', // 调用接口限制 1分钟60次
    ], function($api) {
        // 游客可以访问的api

        // 需要 token 验证的接口
        $api->group(['middleware' => 'refresh.token'], function ($api) {
            // 上传图片
            $api->post('upload/image', 'CommonsController@upload')
                ->name('api.common.upload');
            // 学校列表
            $api->get('school', 'SchoolsController@index')
                ->name('api.school.index');

        });

    });

});

// 小程序API