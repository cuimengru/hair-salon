<?php

namespace App\Admin\Controllers;

use App\Models\Designer;
use App\Models\ReserveOrder;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ReserveOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '预约订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ReserveOrder());

        $grid->filter(function ($filter) {
            $filter->like('designer.name', '设计师');
            $filter->like('service_project', '服务项目');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'))->sortable();
        $grid->column('no', __('订单号'));
        $grid->column('user.name', __('用户姓名'));
        $grid->column('designer.name', __('设计师'))->display(function ($value) {
           if($value == null){
               return '到店分配';
           }else{
               return $value;
           }
        });
        $grid->column('service_project', __('服务项目'));
        $grid->column('phone',__('手机号'));
        $states1 = [
            'on'  => ['value' => 1, 'text' => '不退款', 'color' => 'default'],
            'off' => ['value' => 5, 'text' => '退款', 'color' => 'primary'],
        ];
        $grid->column('status', __('是否退款'))->switch($states1);
        $grid->column('created_at', __('创建时间'));
        $grid->model()->orderBy('id', 'desc');
        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            //$actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ReserveOrder::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('no', __('订单号'));
        $show->field('user.name', __('用户姓名'));
        $show->field('service_project', __('服务项目'));
        $show->field('time', __('预约时间'));
        $show->field('num', __('预约人数'));
        $show->field('phone', __('手机号'));
        $show->field('remark', __('备注'));
        $show->field('money', __('订单总金额'));
        $show->field('payment_method', __('支付方式'))->using(['1' => '余额', '2' => '支付宝','3'=>'微信']);
        $show->field('paid_at', __('支付时间'));
        $show->field('status', __('订单状态'))->using(['1' => '未支付', '2' => '支付中','3'=>'已支付','4'=>'取消','5'=>'退款成功']);
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));
        $show->panel()
            ->tools(function ($tools) {
                //$tools->disableEdit();
                //$tools->disableList();
                $tools->disableDelete();
            });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ReserveOrder());

        $form->select('designer_id',__('设计师'))->options(Designer::all()->pluck('name', 'id'))->required();
        $states1 = [
            'on'  => ['value' => 1, 'text' => '不退款', 'color' => 'default'],
            'off' => ['value' => 5, 'text' => '退款', 'color' => 'primary'],
        ];
        $form->switch('status', __('是否退款'))->states($states1);
        $form->tools(function (Form\Tools $tools) {
            // Disable `Delete` btn.
            $tools->disableDelete();
        });
        return $form;
    }
}
