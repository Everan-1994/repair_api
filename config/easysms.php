<?php

return [
    // HTTP 请求的超时时间（秒）
    'timeout'  => 5.0,

    // 默认发送配置
    'default'  => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'aliyun',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
        'aliyun'   => [
            'access_key_id'     => env('ACCESS_KEY_ID'),
            'access_key_secret' => env('ACCESS_KEY_SECRET'),
            'sign_name'         => '乐之都', // 中文签名直接写，如果在env配置是中文，会报无效签名的错。
        ],
    ],
];