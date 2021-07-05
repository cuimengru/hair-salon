<?php

return [
    'alipay' => [
        'app_id'         => env('ALIPAY_APP_ID'),
        'ali_public_key' => env('ALIPAY_PUBLIC_KEY'),
        'private_key'    => env('ALIPAY_PRIVATE_KEY'),
        //"notify_url" => "http://yansongda.cn/notify.php",
        //"return_url" => "http://yansongda.cn/return.php",
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do", //支付宝网关
        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
        //"mode" => "dev", // optional,设置此参数，将进入沙箱模式
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],

];
