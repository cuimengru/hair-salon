<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\ImagesController;

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
                Route::post('sms', [UserController::class, 'sms']);//发送短信验证码
                Route::post('users', [UserController::class, 'store']);//用户注册
                Route::post('authorizations', [AuthorizationsController::class, 'store']); // 登录
                Route::put('authorizations/current', [AuthorizationsController::class,'update']);// 刷新token
                Route::delete('authorizations/current', [AuthorizationsController::class,'destroy']);// 删除token
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
                    Route::get('user', [UserController::class, 'me']); // 当前登录用户信息
                    Route::get('my', [UserController::class, 'my']); // 我的
                    Route::post('avatar', [UserController::class, 'avatar']); // 修改用户头像

                    Route::post('images', [ImagesController::class,'store']); // 上传图片
                    Route::get('user_addresses', [UserAddressController::class, 'index']);//删除收货地址
                    Route::post('user_addresses', [UserAddressController::class, 'store']);//创建收货地址
                    Route::patch('user_addresses/{id}', [UserAddressController::class, 'update']);//编辑收货地址
                    Route::post('user_addresses/address', [UserAddressController::class, 'destroy']);//删除收货地址
                    Route::post('cart', [CartController::class, 'store']);//添加商品到购物车
                    Route::post('cart/destroy', [CartController::class, 'destroy']);//在购物车中删除商品
                    Route::get('cart/index', [CartController::class, 'index']);//购物车列表

                });
            });
    });
