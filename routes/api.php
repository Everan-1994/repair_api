<?php

$api = app('Illuminate\Routing\Router');

$api->group([
    'namespace'  => 'Api\Plam',
    'middleware' => 'throttle: 5, 1', // 调用接口限制
], function ($api) {
    $api->get('/index', 'UsersController@index')
        ->name('api.user.index');
});