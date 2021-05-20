<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\ProductController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')
    ->namespace('Api')
    ->name('api.v1.')
    ->group(function () {

        Route::middleware('throttle:' . config('api.rate_limits.sign'))
            ->group(function () {
                //Route::post('authorizations', 'AuthorizationsController@store'); // 登录
                //Route::put('authorizations/current', 'AuthorizationsController@update');// 刷新token
                //Route::delete('authorizations/current', 'AuthorizationsController@destroy');// 删除token
            });

        Route::middleware('throttle:' . config('api.rate_limits.access'))
            ->group(function () {
//                // 游客可以访问的接口
                Route::get('index', [IndexController::class, 'index']);//猜你喜欢首页
                Route::get('products/index', [ProductController::class, 'index']);//商城产品首页
                Route::get('products/search', [ProductController::class, 'search']);//商城产品搜索
                Route::get('products/{product}', [ProductController::class, 'show']);//商城产品详情

                // 登录后可以访问的接口
                Route::middleware('auth:api')->group(function () {

                });
            });
    });
