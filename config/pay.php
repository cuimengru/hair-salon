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
        'appid'      => env('WECHAT_PAY_APP_ID'),
        'miniapp_id' => env('MINIAPP_ID'), // hyh新增小程序支付 APPID 20211014
        'mch_id'      => env('WECHAT_PAY_MCH_ID'),
        'key'         => env('WECHAT_PAY_KEY'),
        'cert_client' => resource_path('wechat_pay/apiclient_cert.pem'),
        'cert_key'    => resource_path('wechat_pay/apiclient_key.pem'),
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],

];
