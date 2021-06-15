<?php

namespace App\Admin\Controllers;

use App\Models\Designer;
use App\Models\ReserveInformation;
use App\Models\ReserveOrder;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OffreserveOrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '线下预约订单';

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
        $grid->column('user.nickname', __('用户昵称'));
        $grid->column('designer.name', __('设计师'))->display(function ($value) {
            if($value == null){
                return '到店分配';
            }else{
                return $value;
            }
        });
        $grid->column('service_project', __('服务项目'));
        $grid->column('phone',__('手机号'));
        $grid->column('reserve_date',__('预约时间'));
        /*$states1 = [
            'on'  => ['value' => 1, 'text' => '不退款', 'color' => 'default'],
            'off' => ['value' => 5, 'text' => '退款', 'color' => 'primary'],
        ];
        $grid->column('status', __('是否退款'))->switch($states1);*/
        $grid->status('订单状态')->display(function($value) {
            $statusMap = ReserveOrder::$statusMap[$value];
            return "<span class='label label-success'>{$statusMap}</span>";
        });
        $grid->column('created_at', __('创建时间'));
        $grid->model()->orderBy('id', 'desc');
        // 禁用创建按钮，后台不需要创建订单
        //$grid->disableCreateButton();
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
        $grid->model()->where('type', '=',2);

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
        $show->field('user.nickname', __('用户昵称'));
        $show->field('service_project', __('服务项目'));
        $show->field('reserve_date', __('预约时间'));
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

        $form->text('no', __('订单号'));
        $form->select('reserve_id', __('预约信息'))->options(ReserveInformation::all()->pluck('designer.name','id'))
            ->load('designer_id','/api/designer');
        $form->select('designer_id', __('发型师'))->options(function ($id) {
            return $id;
        })->required();
        $form->select('user_id', __('线下用户昵称'))->options(User::all()->pluck('nickname','id'));
        $form->number('service_project', __('Service project'));
        $form->date('date', __('Date'))->default(date('Y-m-d'));
        $form->text('time', __('Time'));
        $form->number('num', __('Num'))->default(1);
        $form->mobile('phone', __('Phone'));
        $form->text('remark', __('Remark'));
        $form->decimal('money', __('Money'));
        $form->text('payment_method', __('Payment method'));
        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        $form->text('payment_no', __('Payment no'));
        $form->number('status', __('Status'));
        $form->switch('reviewed', __('Reviewed'));
        $form->number('type', __('Type'));

        return $form;
    }

}
