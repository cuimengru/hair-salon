<?php

namespace App\Admin\Controllers;

use App\Models\ProductionFace;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductionFaceController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '脸型';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ProductionFace());

        $grid->column('id', __('Id'));
        $grid->column('name', __('脸型'));
        $grid->column('created_at', __('创建时间'));
       /* $grid->column('updated_at', __('Updated at'));*/
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
        $show = new Show(ProductionFace::findOrFail($id));

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
        $form = new Form(new ProductionFace());

        $form->text('name', __('脸型'));

        return $form;
    }
}
