<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UsersController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '用户';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('Id'));
        $grid->column('avatar_url', __('头像'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:30px;max-height:30px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });
        $grid->column('name', __('姓名'));
        $grid->column('nickname', __('昵称'));
        $grid->column('phone', __('手机号'));
        $grid->column('email', __('邮箱'));
        $grid->email_verified_at('已验证邮箱')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->status('审核状态')->radio([
            0 => '未审核',
            1 => '已审核',
            -1 => '审核中',
        ])->help('可编辑');
        $grid->column('created_at', __('创建时间'));
        //$grid->column('updated_at', __('更新时间'));
        $grid->actions(function ($actions) {
            $actions->disableEdit();// 去掉删除
        });

        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->model()->orderBy('id', 'desc');
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->avatar_url()->image();
        $show->field('name', __('姓名'));
        $show->field('nickname', __('昵称'));
        $show->field('phone', __('手机号'));
        $show->field('email', __('邮箱'));
        $show->field('email_verified_at', __('邮箱验证'));
        $show->field('introduce', __('简介'));
        $show->field('integral', __('积分'));
        $show->field('balance', __('余额'));
        $show->field('status', __('审核状态'))->using(['1' => '已审核', '0' => '未审核','-1'=>'审核中']);
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User());
        $form->image('avatar', __('头像'));
        $form->text('name', __('姓名'));
        $form->text('nickname', __('昵称'));
        if ($form->isEditing()) {
            $form->text('phone', __('手机号'));
            $form->text('email', __('邮箱'));
            $form->password('password', __('密码'))->help('不修改密码无需填写 默认密码 123456');
        }
        if ($form->isCreating()) {
            $form->mobile('phone', __('手机号'))->required();
            $form->text('email', __('邮箱'))->required();
            $form->password('password', __('密码'))->default('123456')->required()->help('默认密码 123456');
        }
        $form->textarea('introduce', __('简介'));
        $form->text('integral', __('积分'))->default(0.00);
        $form->text('balance', __('余额'))->default(0.00);
        $form->radioCard('status', __('审核状态'))->options(['0' => '未审核', '1' => '已审核','-1'=>'审核中'])->default('0');

        return $form;
    }
}
