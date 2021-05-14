<?php

namespace App\Admin\Controllers;

use App\Models\Fashion;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class FashionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Fashion';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Fashion());
        $grid->filter(function ($filter) {
            $filter->like('title', '标题');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'))->sortable();
        $grid->column('title', __('标题'))->limit(20);
        $grid->column('thumb_url', __('封面图片'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:50px;max-height:50px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });
        /*$grid->column('video', __('Video'));
        $grid->column('description', __('Description'));
        $grid->column('content', __('Content'));
        $grid->column('order', __('Order'));
        $grid->column('is_recommend', __('Is recommend'));*/
        $grid->column('created_at', __('创建时间'));
        //$grid->column('updated_at', __('Updated at'));
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
        $show = new Show(Fashion::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('thumb', __('Thumb'));
        $show->field('video', __('Video'));
        $show->field('description', __('Description'));
        $show->field('content', __('Content'));
        $show->field('order', __('Order'));
        $show->field('is_recommend', __('Is recommend'));
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
        $form = new Form(new Fashion());

        $form->text('title', __('标题'))->required();
        $form->image('thumb', __('封面图片'))->required();
        $form->file('video', __('视频'));
        $form->textarea('description', __('描述'));
        $form->editor('content', __('内容'))->required();
        $form->number('order', __('排序'))->default(0)->help('越小越靠前');
        //$form->number('is_recommend', __('Is recommend'));

        return $form;
    }
}
