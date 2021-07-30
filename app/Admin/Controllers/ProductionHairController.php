<?php

namespace App\Admin\Controllers;

use App\Models\ProductionHair;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductionHairController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '烫染';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductionHair());

        $grid->column('id', __('Id'));
        $grid->column('name', __('名称'));
        //$grid->column('updated_at', __('Updated at'));
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
        $show = new Show(ProductionHair::findOrFail($id));

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
        $form = new Form(new ProductionHair());

        $form->text('name', __('名称'));

        return $form;
    }
}
