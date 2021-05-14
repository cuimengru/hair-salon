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
    $router->resource('education_cultures', EducationCultureController::class);//教育类文化中心
    $router->resource('train_cultures', TrainCultureController::class);//培训类文化中心
    $router->resource('offline_cultures', OfflineCultureController::class);//线下活动类文化中心
    $router->resource('advert_categories', AdvertCategoryController::class);//广告分类
    $router->get('api/advert_categories', 'AdvertCategoryController@apiIndex');//关联广告类型的接口
    $router->resource('adverts', AdvertController::class);//广告管理
    $router->resource('designers', DesignerController::class);//设计师信息管理
    $router->resource('productions', ProductionController::class);//作品管理
    $router->resource('fashions', FashionController::class);//时尚资讯管理
    $router->resource('help_centers', HelpCenterController::class);//帮助信息管理
    $router->resource('feedback', FeedbackController::class);//问题反馈
    $router->resource('reserve_informations', ReserveInformationController::class);//预约信息管理
    $router->get('api/designer', 'DesignerController@apiIndex');//关联设计师的接口
    $router->resource('reserve_orders', ReserveOrderController::class);//预约订单管理
    $router->resource('designer_comments', DesignersCommentController::class);//设计师评价管理
    $router->resource('product_comments', ProductsCommentController::class);//商品评价管理
});
