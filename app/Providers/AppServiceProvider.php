<?php

namespace App\Providers;

use Monolog\Logger;
use Yansongda\Pay\Pay;
use Encore\Admin\Config\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //往服务容器中注入一个名为 alipay 的单例对象 ，商品支付宝
        $this->app->singleton('alipay',function (){
            $config = config('pay.alipay');
            $config['notify_url'] = route('api.v1.payment.alipay.notify');
            $config['return_url'] = route('api.v1.payment.alipay.return');
            // 判断当前项目运行环境是否为线上环境
            if (app()->environment() !== 'production') {
                $config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function () {
            $config = config('pay.wechat');
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });

        //预约订单支付宝
        $this->app->singleton('reservealipay',function (){
            $config = config('pay.alipay');
            $config['notify_url'] = route('api.v1.payment.reservealipay.notify');
            $config['return_url'] = route('api.v1.payment.reservealipay.return');
            // 判断当前项目运行环境是否为线上环境
            if (app()->environment() !== 'production') {
                $config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        //充值支付宝
        $this->app->singleton('balancealipay',function (){
            $config = config('pay.alipay');
            $config['notify_url'] = route('api.v1.payment.balancealipay.notify');
            $config['return_url'] = route('api.v1.payment.balancealipay.return');
            // 判断当前项目运行环境是否为线上环境
            if (app()->environment() !== 'production') {
                $config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        URL::forceScheme('https'); //强制 https
        //Order::observe(OrderObserver::class); // 订单观察者 处理删除或者新增订单后的操作 TODO
        //Resource::withoutWrapping();// 资源返回不包裹在 data 里面
        $table = config('admin.extensions.config.table', 'admin_config');
        if (Schema::hasTable($table)) {
            Config::load();
            //Schema::defaultStringLength(191);
        }
        Horizon::auth(function ($request) {
            return true;
        });


    }
}
