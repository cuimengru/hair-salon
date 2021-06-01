<?php

namespace App\Admin\Controllers;

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
        $grid->column('created_at', __('创建时间'));
        $grid->model()->orderBy('id', 'desc');
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
        //$show->field('reserve_id', __('Reserve id'));
        $show->field('user.name', __('用户姓名'));
        $show->field('service_project', __('服务项目'));
        $show->field('time', __('预约时间'));
        $show->field('num', __('预约人数'));
        $show->field('phone', __('手机号'));
        $show->field('remark', __('备注'));
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));

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

        $form->number('reserve_id', __('Reserve id'));
        $form->text('user_id', __('User id'));
        $form->text('service_project', __('Service project'));
        $form->text('time', __('Time'));
        $form->number('num', __('Num'))->default(1);
        $form->mobile('phone', __('Phone'));
        $form->text('remark', __('Remark'));

        return $form;
    }
}
