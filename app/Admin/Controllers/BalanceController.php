<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\AdminController;
Use Encore\Admin\Widgets\Table;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BalanceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品余额变化';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());
        $grid->filter(function ($filter) {
            $filter->like('user.nickname', __('用户'));
            $filter->like('user.phone', __('手机号'));
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'));
        //$grid->column('no', __('No'));
        $grid->column('user.nickname', __('用户'));
        $grid->column('user.phone', __('手机号'));
        $grid->column('remaining_balance', __('原余额'));
        $grid->column('total_amount', __('余额变化'))->display(function ($value) {
            if($value>0){
                return '-'.$value;
            }else{
                return $value;
            }
        });
        $grid->column('balance', __('变化后余额'));
        $grid->column('paid_at', __('创建时间'));
        $grid->model()->where('payment_method', '=',1);
        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        $grid->model()->orderBy('paid_at', 'desc');
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('查看余额')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.orders.balanceshow', ['order' => Order::find($id)]));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order());

        $form->text('no', __('No'));
        $form->number('user_id', __('User id'));
        $form->textarea('address', __('Address'));
        $form->decimal('total_amount', __('Total amount'));
        $form->textarea('remark', __('Remark'));
        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', __('Payment method'));
        $form->text('payment_no', __('Payment no'));
        $form->text('status', __('Status'));
        $form->text('refund_status', __('Refund status'));
        $form->text('refund_no', __('Refund no'));
        $form->switch('closed', __('Closed'));
        $form->switch('reviewed', __('Reviewed'));
        $form->text('ship_status', __('Ship status'));
        $form->textarea('ship_data', __('Ship data'));
        $form->textarea('extra', __('Extra'));

        return $form;
    }
}
