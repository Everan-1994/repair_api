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
        // 登录
        $api->post('tts', 'AuthorizationsController@tts')
            ->name('api.authorizations.tts');
        // 小程序登录
        $api->post('weapp/authorizations', 'AuthorizationsController@weappStore')
            ->name('api.weapp.authorizations.store');
        // 退出登陆
        $api->delete('logout', 'AuthorizationsController@logout')
            ->name('api.authorizations.index');
    });

    $api->group([
        'middleware' => 'throttle: 120, 1', // 调用接口限制 1分钟120次
    ], function ($api) {
        // 游客可以访问的api

        // 需要 token 验证的接口
        $api->group(['middleware' => 'refresh.token'], function ($api) {
            // 上传图片
            $api->post('upload/image', 'CommonsController@upload')
                ->name('api.common.upload');
            // 删除图片
            $api->delete('del/image', 'CommonsController@delImage')
                ->name('api.common.delImage');
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
            // 修改密码
            $api->patch('user/changePwd', 'UsersController@changePwd')
                ->name('api.user.changePwd');
            // 区域列表
            $api->get('area', 'AreasController@index')
                ->name('api.area.index');
            // 获取学校区域
            $api->get('area/select', 'AreasController@areaList')
                ->name('api.area.areaList');
            // 新增区域
            $api->post('area', 'AreasController@store')
                ->name('api.area.store');
            // 新增区域
            $api->patch('area/{area}', 'AreasController@update')
                ->name('api.area.update');
            // 区域详情
            $api->get('area/{area}', 'AreasController@show')
                ->name('api.area.show');
            // 变更状态
            $api->patch('area/status/{area}', 'AreasController@changeStatus')
                ->name('api.member.changeStatus');
            // 删除区域
            $api->delete('area/{area}', 'AreasController@del')
                ->name('api.area.del');

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
            // 获取申报列表
            $api->get('orders/repair', 'OrdersController@getOrderList')
                ->name('api.order.getOrderList');
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
            // 删除类型
            $api->delete('type/{type}', 'TypesController@del')
                ->name('api.type.del');

            // 派工
            $api->post('dispatch', 'OrdersController@dispatchs')
                ->name('api.order.dispatchs');
            // 完工
            $api->post('orders/fixedOrder', 'OrdersController@fixedOrder')
                ->name('api.order.fixedOrder');
            // 评价
            $api->post('orders/evaluateOrder', 'OrdersController@evaluateOrder')
                ->name('api.order.evaluateOrder');
            // 申诉
            $api->post('orders/statementOrder', 'OrdersController@statementOrder')
                ->name('api.order.statementOrder');

            // 通知列表
            $api->get('user/notifications', 'NotificationsController@index')
                ->name('api.user.notifications.index');
            // 通知统计
            $api->get('user/notifications/stats', 'NotificationsController@stats')
                ->name('api.user.notifications.stats');
            // 设置消息已读
            $api->put('user/read/notifications', 'NotificationsController@read')
                ->name('api.user.notifications.read');

            // 文章列表
            $api->get('article', 'ArticlesController@index')
                ->name('api.article.index');
            // 新增文章
            $api->post('article', 'ArticlesController@store')
                ->name('api.article.store');
            // 更新文章
            $api->patch('article', 'ArticlesController@update')
                ->name('api.article.update');
            // 变更状态
            $api->patch('article/status', 'ArticlesController@changeStatus')
                ->name('api.article.changeStatus');
            // 删除文章
            $api->delete('article/{article}', 'ArticlesController@del')
                ->name('api.article.del');
            // 文章详情
            $api->get('article/{article}', 'ArticlesController@show')
                ->name('api.article.show');
            // 文章图片
            $api->post('article/image', 'ArticlesController@upload')
                ->name('api.article.upload');
            // 文章阅读数
            $api->put('article/views', 'ArticlesController@views')
                ->name('api.article.views');

            // 首页接口
            // 用户总数
            $api->get('user/count', 'UsersController@getUserCount')
                ->name('api.user.getUserCount');
            // 今日新工单 & 今日已完成工单 & 工单总数
            $api->get('order/count', 'OrdersController@getOrderCount')
                ->name('api.order.getOrderCount');
            // 好评率
            $api->get('order/evaluate', 'OrdersController@getRepairEvaluate')
                ->name('api.order.getRepairEvaluate');
            // 申报类型比例
            $api->get('order/type/ratio', 'TypesController@getOrderTypeRatio')
                ->name('api.type.getOrderTypeRatio');
            // 上周申报量
            $api->get('week/order', 'OrdersController@getWeekOrder')
                ->name('api.order.getWeekOrder');
            // 每月申报量
            $api->get('month/order', 'OrdersController@getMonthOrder')
                ->name('api.order.getMonthOrder');
            // 每月申报量
            $api->get('user/order', 'UsersController@getUserOrderCount')
                ->name('api.user.getUserOrderCount');
        });

    });
});