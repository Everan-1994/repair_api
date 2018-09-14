<?php

function createOrderNm()
{

    $year_code = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    $date_code = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A',

        'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M',

        'N', 'O', 'P', 'Q', 'R', 'T', 'U', 'V', 'W', 'X', 'Y',
    ];

    // 一共15位订单号,同一秒内重复概率1/10000000,26年一次的循环

    $order_sn = $year_code[(intval(date('Y')) - 2010) % 26] . //年 1位

        strtoupper(dechex(date('m'))) . //月(16进制) 1位

        $date_code[intval(date('d'))] . //日 1位

        substr(time(), -5) . substr(microtime(), 2, 5) . //秒 5位 // 微秒 5位

        sprintf('%02d', rand(0, 99)); //  随机数 2位

    return $order_sn;

}