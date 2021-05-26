<?php

namespace App\Providers;

use Encore\Admin\Config\Config;
use Illuminate\Http\Resources\Json\Resource;
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
        //
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
        Resource::withoutWrapping();// 资源返回不包裹在 data 里面
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
