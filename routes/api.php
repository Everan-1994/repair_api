<?php

$api = app('Illuminate\Routing\Router');

// 后台API
$api->group([
    'namespace'  => 'Api',
], function ($api) {

    $api->group([
        'middleware' => 'throttle: 20, 1', // 调用接口限制 1分钟10次
    ], function ($api) {
        // 图片验证码
        $api->get('captchas/{captcha_key}', 'CaptchasController@store')
            ->name('api.captchas.store');
        // 手机图片验证码
        $api->post('captchas', 'CaptchasController@captchaForPhone')
            ->name('api.captchas.captchaForPhone');
        // 短信验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        // 登录
        $api->post('login', 'AuthorizationsController@login')
            ->name('api.authorizations.login');
        // 小程序登录
        $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
            ->name('api.weapp.authorizations.store');
        // 退出登陆
        $api->delete('logout', 'AuthorizationsController@logout')
            ->name('api.authorizations.index');
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
            // 新增学校
            $api->post('school', 'SchoolsController@store')
                ->name('api.school.store');
            // 学校信息更新
            $api->patch('school/{school}', 'SchoolsController@update')
                ->name('api.school.update');
            // 学校详情
            $api->get('school/{school}', 'SchoolsController@show')
                ->name('api.school.show');
            // 删除学校
            $api->delete('school/{school}', 'SchoolsController@destroy')
                ->name('api.school.destroy');
            // 下拉选项--学校列表
            $api->get('getSchoolList', 'SchoolsController@getSchoolList')
                ->name('api.school.getSchoolList');
            // 客户列表
            $api->get('customer', 'CustomersController@index')
                ->name('api.customer.index');
            // 客户信息
            $api->get('customer/{user}', 'CustomersController@show')
                ->name('api.customer.show');
            // 新增客户
            $api->post('customer', 'CustomersController@store')
                ->name('api.customer.store');
            // 更新客户
            $api->patch('customer/{user}', 'CustomersController@update')
                ->name('api.customer.update');
            // 获取个人信息
            $api->get('user', 'UsersController@info')
                ->name('api.user.info');
            // 小程序用户更新
            $api->put('customer', 'CustomersController@weappUserUpdate')
                ->name('api.customer.weappUserUpdate');
        });

    });
});