<?php

namespace App\Admin\Controllers;

use App\Models\Report;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ReportsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '举报';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Report());
        $grid->filter(function ($filter) {
            $filter->like('user.nickname', __('举报用户昵称'));
            $filter->like('community_id', '晒单id');
            $filter->like('community.title', '晒单标题');
            $filter->between('created_at','创建时间')->datetime();
        });

        $grid->column('id', __('Id'));
        $grid->column('community_id', __('晒单id'));
        $grid->column('community.title', __('晒单标题'));
        $grid->column('user.nickname', __('举报用户昵称'));
        $grid->column('reason', __('理由'))->limit(20);
        $grid->column('created_at', __('创建时间'));
        $grid->actions(function ($actions) {
            //$actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });
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
        $show = new Show(Report::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('community_id', __('晒单id'));
        $show->field('community.title', __('晒单标题'));
        $show->field('user.nickname', __('举报用户昵称'));
        $show->field('user.phone', __('举报用户手机号'));
        $show->field('reason', __('理由'));
        $show->field('created_at', __('创建时间'));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
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
        $form = new Form(new Report());

        $form->number('user_id', __('User id'));
        $form->number('community_id', __('Community id'));
        $form->text('reason', __('Reason'));

        return $form;
    }
}
