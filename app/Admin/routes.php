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
    $router->resource('categories', CategoryController::class);//集品商品类目管理
    $router->get('categories/create', 'CategoryController@create');// 创建集品商品类目管理表单
    $router->post('categories', 'CategoryController@store'); // 新增集品商品类目管理
    $router->get('categories/{id}/edit', 'CategoryController@edit');// 修改集品商品类目管理表单
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
    $router->resource('service_projects', ServiceProjectController::class);//服务项目管理
    $router->resource('balances', BalanceController::class);//余额管理
    $router->get('balances/{balance}', 'BalanceController@show')->name('admin.orders.balanceshow');//余额详情
    $router->resource('product_labels', ProductLabelController::class);//产品标签管理
    $router->resource('sensitive_words', SensitiveWordController::class);//敏感词管理
    $router->resource('self_categories', SelfCategoryController::class);//自营商品类目管理
    $router->get('self_categories/create', 'SelfCategoryController@create');// 创建自营商品类目管理表单
    $router->post('self_categories', 'SelfCategoryController@store'); // 新增自营商品类目管理
    $router->get('self_categories/{id}/edit', 'SelfCategoryController@edit');// 修改自营商品类目管理表单
    $router->resource('idle_categories', IdleCategoryController::class);//闲置商品类目管理
    $router->get('idle_categories/create', 'IdleCategoryController@create');// 创建闲置商品类目管理表单
    $router->post('idle_categories', 'IdleCategoryController@store'); // 新增闲置商品类目管理
    $router->get('idle_categories/{id}/edit', 'IdleCategoryController@edit');// 修改闲置商品类目管理表单
    $router->resource('designer_labels', DesignerLabelController::class); //设计师标签管理
    $router->resource('communities', CommunityController::class); //社区管理
    $router->resource('worktimes', WorktimeController::class); //工作时间管理
    $router->resource('leavetimes', LeavetimeController::class); //请假管理
    $router->resource('offline_users', OfflineUserController::class);//线下用户管理
    $router->resource('offreserve_orders', OffreserveOrderController::class);//线下预约订单管理
});
