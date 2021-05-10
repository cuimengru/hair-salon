<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->resource('users', UsersController::class);// 用户管理
    $router->resource('categories', CategoryController::class);//商品类目管理
    $router->get('categories/create', 'CategoryController@create');// 创建商品类目管理表单
    $router->post('categories', 'CategoryController@store'); // 新增商品类目管理
    $router->get('categories/{id}/edit', 'CategoryController@edit');// 修改商品类目管理表单
    $router->get('api/categories', 'CategoriesController@apiIndex');
    $router->resource('products', ProductsController::class);//商品信息管理(集品类型)
    $router->resource('self_products', SelfProductsController::class);//商品信息管理(自营类型)
    $router->resource('idle_products', IdleProductsController::class);//商品信息管理(闲置类型)
    $router->resource('orders', OrdersController::class);//订单管理
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');//订单详情
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');//订单物流
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.handle_refund');
});
