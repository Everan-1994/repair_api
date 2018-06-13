<?php

$api = app('Illuminate\Routing\Router');

// 后台API
$api->group([
    'namespace' => 'Api',
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
        'middleware' => 'throttle: 120, 1', // 调用接口限制 1分钟60次
    ], function ($api) {
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
            // 区域列表
            $api->get('area', 'AreasController@index')
                ->name('api.area.index');
            // 获取学校区域
            $api->get('area/select', 'AreasController@areaList')
                ->name('api.area.areaList');
            // 新增区域
            $api->post('area', 'AreasController@store')
                ->name('api.area.store');
            // 小程序用户更新
            $api->put('customer', 'CustomersController@weappUserUpdate')
                ->name('api.customer.weappUserUpdate');
            // 提交申报
            $api->post('order', 'OrdersController@store')
                ->name('api.order.store');
            // 提交申报(编辑)
            $api->put('order/{order}', 'OrdersController@update')
                ->name('api.order.update');
            // 获取申报列表
            $api->get('orders', 'OrdersController@index')
                ->name('api.order.index');
            // 获取申报详情
            $api->get('orders/{order}', 'OrdersController@show')
                ->name('api.order.show');
            // 删除申报
            $api->delete('orders/{order}', 'OrdersController@del')
                ->name('api.order.del');
            // 申报回复
            $api->put('processes', 'OrdersController@replies')
                ->name('api.order.replies');
            // 获取用户列表
            $api->get('member', 'MembersController@index')
                ->name('api.member.index');
            // 变更身份
            $api->patch('member', 'MembersController@changeIdentify')
                ->name('api.member.changeIdentify');
            // 变更状态
            $api->patch('member/status', 'MembersController@changeStatus')
                ->name('api.member.changeStatus');

            // 类型列表
            $api->get('type', 'TypesController@index')
                ->name('api.type.index');
            // 类型详情
            $api->get('type/{type}', 'TypesController@show')
                ->name('api.type.show');
            // 类型列表
            $api->post('type', 'TypesController@store')
                ->name('api.type.store');
            // 类型列表
            $api->patch('type/{type}', 'TypesController@update')
                ->name('api.type.update');
        });

    });
});