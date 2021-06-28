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
use App\Http\Controllers\Api\HelpCenterController;
use App\Http\Controllers\Api\ProductionController;
use App\Http\Controllers\Api\DesignerController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\CultureController;
use App\Http\Controllers\Api\UserLikeController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\ReserveInformationController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ProductOrderController;
use App\Http\Controllers\Api\DesignersController;
use App\Http\Controllers\Api\PaymentController;

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
                Route::post('forget', [UserController::class, 'forgetPassword']);//用户忘记密码
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

                Route::get('help_center', [HelpCenterController::class, 'index']); //帮助中心列表
                Route::get('help_center/{id}', [HelpCenterController::class, 'show']);  //某个帮助中心详情

                Route::get('production', [ProductionController::class, 'index']);  //作品首页
                Route::get('allproduction', [ProductionController::class, 'allIndex']);  //全部作品列表
                Route::get('production/{id}', [ProductionController::class, 'show']);  //作品详情
                /*Route::get('designers', [DesignerController::class, 'index']);//设计师列表*/
                Route::get('designer/{id}', [DesignerController::class, 'show']);//某个设计师详情

                Route::get('products_category', [CategoriesController::class, 'allcategory']);//集品类商品分类
                Route::get('self_categories', [CategoriesController::class, 'selfcategory']);//自营类商品分类
                Route::get('idle_categories', [CategoriesController::class, 'idlecategory']);//闲置类商品分类
                Route::get('category/products', [ProductController::class, 'allproducts']);//根据分类查询商品

                Route::get('culture', [CultureController::class, 'index']);//文教娱乐列表
                Route::get('culture/{id}', [CultureController::class, 'show']);//文教娱乐详情
                Route::get('fashion', [CultureController::class, 'fashionIndex']);//时尚资讯列表
                Route::get('fashion/{id}', [CultureController::class, 'fashionShow']);//时尚资讯详情

                Route::get('community', [CommunityController::class, 'index']); //社区列表
                Route::get('product/index/{id}', [CommentController::class, 'productIndex']);//某个产品的评价列表
                Route::get('reserve/index/{id}', [CommentController::class, 'reserveIndex']);//某个设计师的评价列表
                Route::get('designers/index', [DesignersController::class,'index']); //发型师列表
                Route::get('designers/show/{id}', [DesignersController::class, 'show']);//某个发型师详情
                Route::get('jinzhi/about', [IndexController::class, 'jinzhido']);//关于锦之都



                // 登录后可以访问的接口
                Route::middleware('auth:api')->group(function () {
                    Route::get('user', [UserController::class, 'me']); // 当前登录用户信息
                    Route::get('my', [UserController::class, 'my']); // 我的
                    Route::post('avatar', [UserController::class, 'avatar']); // 修改用户头像
                    Route::post('reset', [UserController::class, 'resetPassword']); // 修改密码
                    Route::patch('user', [UserController::class, 'update']); // 编辑用户
                    Route::post('images', [ImagesController::class,'store']); // 上传图片

                    Route::post('user_addresses', [UserAddressController::class, 'store']);//创建收货地址
                    Route::get('user_addresses', [UserAddressController::class, 'index']);//收货地址列表
                    Route::patch('user_addresses/{id}', [UserAddressController::class, 'update']);//编辑收货地址
                    Route::post('user_addresses/address', [UserAddressController::class, 'destroy']);//删除收货地址

                    Route::get('cart/index', [CartController::class, 'index']);//购物车列表
                    Route::post('cart', [CartController::class, 'store']);//添加商品到购物车
                    Route::patch('cart/{id}', [CartController::class, 'update']); // 减去购物车商品数量
                    Route::post('cart/destroy', [CartController::class, 'destroy']);//在购物车中删除商品


                    /*Route::post('favor/{designer}/designer', [DesignerController::class, 'favordesigner']);//收藏设计师
                    Route::delete('unfavor/{designer}/designer', [DesignerController::class, 'disfavor']);//取消收藏设计师
                    Route::get('designer/followlist', [DesignerController::class, 'followlist']);//收藏设计师列表*/
                    //Route::post('designer/followlist', [HelpCenterController::class, 'followlists']);//收藏设计师
                    Route::post('favorite/{designer}/designer',[DesignersController::class,'favordesigner']);//收藏发型师
                    Route::delete('unfavorite/{designer}/designer',[DesignersController::class,'disdesigner']); //取消发型师
                    Route::get('designers/favorlist',[DesignersController::class,'favorlist']); //收藏发型师列表

                    Route::post('favor/{production}/production', [ProductionController::class, 'favor']);  //收藏作品
                    Route::delete('unfavor/{production}/production', [ProductionController::class, 'disfavor']);  //取消收藏作品
                    Route::post('production/followlist', [ProductionController::class, 'followlist']);//收藏作品列表
                    Route::post('feedback', [HelpCenterController::class, 'storefeedback']); //提交反馈问题

                    Route::post('like/product', [UserLikeController::class, 'likeProduct']); //创建集品商品浏览记录
                    Route::post('like/idleproduct', [UserLikeController::class, 'likeIdleProduct']); //创建转售商品浏览记录
                    Route::post('like/designer', [UserLikeController::class, 'likeDesigner']); //创建设计师浏览记录
                    Route::post('like/production', [UserLikeController::class, 'likeProduction']); //创建作品浏览记录
                    Route::get('like/list', [UserLikeController::class, 'likeList']); //浏览列表

                    Route::post('community', [CommunityController::class, 'store']); //发布社区内容
                    Route::post('message', [CommunityController::class, 'storeMessage']); //创建社区评论
                    Route::post('community/like', [CommunityController::class, 'storelike']); //创建社区评论点赞
                    Route::post('community/unlike', [CommunityController::class, 'deletelike']); //取消社区评论点赞

                    Route::post('favor/{product}/product', [ProductController::class, 'favor']);//收藏商品
                    Route::delete('unfavor/{product}/product', [ProductController::class, 'disfavor']);  //取消收藏商品
                    Route::post('product/followlist', [ProductController::class, 'followlist']);//收藏商品列表

                    Route::get('worktime', [ReserveInformationController::class, 'worktime']); //工作时间
                    Route::get('service_projects', [ReserveInformationController::class, 'service']); //服务项目
                    Route::get('work/day', [ReserveInformationController::class, 'day']); //某个设计师工作时间
                    Route::get('reserve/designer', [ReserveInformationController::class, 'designerIndex']); //可预约的设计师列表
                    Route::post('reserve/orders', [ReserveInformationController::class, 'store']);//创建预约订单
                    Route::patch('reserve/time/{id}', [ReserveInformationController::class, 'updateTime']);//修改预约时间
                    Route::get('reserve/order/{id}', [ReserveInformationController::class, 'show']);//某个预约订单详情

                    Route::post('product/comment', [CommentController::class, 'productStore']);//商品订单评价
                    Route::post('reserve/comment', [CommentController::class, 'reserveStore']);//预约订单评价

                    Route::post('product/order', [ProductOrderController::class, 'store']);//创建商品订单
                    Route::get('product/orderIndex', [ProductOrderController::class, 'index']);//全部订单
                    Route::get('product/order/{id}', [ProductOrderController::class, 'show']);//某个商品订单详情
                    Route::get('product/shipOrder/{id}', [ProductOrderController::class, 'shipOrder']);//某个商品订单确认收货
                    Route::get('product/refundOrder/{id}', [ProductOrderController::class, 'refundOrder']);//某个商品订单取消退款

                    Route::get('product/pay/{id}', [PaymentController::class, 'productStore']);//提交商品订单支付
                    Route::get('reserve/pay/{id}', [PaymentController::class, 'reserveStore']);//提交预约订单支付

                    Route::post('product/refund/{id}', [ProductOrderController::class, 'refund']);//某个商品订单退款
                    Route::get('balance/list', [PaymentController::class, 'balance']);//我的余额管理
                    Route::get('product/logistics', [ProductOrderController::class, 'logistics']);//查看物流

                });
            });
    });
