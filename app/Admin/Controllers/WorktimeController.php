<?php

namespace App\Admin\Controllers;

use App\Models\Worktime;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WorktimeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '工作时间';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Worktime());

        $grid->column('id', __('Id'));
        $grid->column('time', __('时间'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            //$actions->disableDelete();
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
        $show = new Show(Worktime::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('time', __('Time'));
        $show->field('order', __('Order'));
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
        $form = new Form(new Worktime());

        $form->text('time', __('时间'));
        $form->number('order', __('排序'))->default(0)->help('越小越靠前');

        return $form;
    }
}
