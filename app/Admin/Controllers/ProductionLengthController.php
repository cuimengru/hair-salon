<?php

namespace App\Admin\Controllers;

use App\Models\ProductionLength;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductionLengthController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '作品长度';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductionLength());
        $grid->filter(function ($filter) {
            $filter->like('name', __('长度'));
            $filter->between('paid_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'));
        $grid->column('name', __('长度'));
        $grid->column('created_at', __('创建时间'));
        $grid->actions(function ($actions) {
            //$actions->disableDelete();
            $actions->disableView();
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
        $show = new Show(ProductionLength::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
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
        $form = new Form(new ProductionLength());

        $form->text('name', __('长度'));

        return $form;
    }
}
