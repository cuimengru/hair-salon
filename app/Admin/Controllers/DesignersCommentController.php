<?php

namespace App\Admin\Controllers;

use App\Models\Comment;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class DesignersCommentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '设计师评价';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Comment());
        $grid->filter(function ($filter) {
            $filter->like('user.name', __('用户'));
            $filter->like('designer.name', '设计师');
            $filter->between('created_at','创建时间')->datetime();
        });

        $grid->column('id', __('Id'))->sortable();
        //$grid->column('type', __('Type'));
        $grid->column('user.name', __('用户'));
        $grid->column('reserveorder_id', __('预约订单ID'));
        $grid->column('designer.name', __('设计师'));
        $grid->column('rate', __('评分'));
        /*$grid->column('render_content', __('Render content'));
        $grid->column('render_image', __('Render image'));
        $grid->column('render_video', __('Render video'));*/
        $states1 = [
            'on'  => ['value' => 0, 'text' => '未审核', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '已审核', 'color' => 'primary'],
        ];
        $grid->column('status', __('状态'))->switch($states1);
        $grid->column('created_at', __('创建时间'));
        $grid->model()->where('type', '=',1);
        $grid->disableCreateButton();
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
        $show = new Show(Comment::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user.name', __('用户'));
        $show->field('reserveorder_id', __('预约订单ID'));
        $show->field('designer.name', __('设计师'));
        $show->field('rate', __('评分'));
        $show->field('render_content', __('评论内容'));
        $show->field('render_image', __('Render image'));
        $show->field('video_url', __('视频'))->unescape()->as(function ($video_url) {
            return "<video width='320' height='320' controls>
                <source src='{$video_url}' type='video/mp4'>
            </video>";
        });
        //$show->field('render_video', __('Render video'));
        $show->field('status', __('状态'))->using(['1' => '已审核', '0' => '未审核']);
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
        $form = new Form(new Comment());
        $states1 = [
            'on'  => ['value' => 0, 'text' => '未审核', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '已审核', 'color' => 'primary'],
        ];
        $form->switch('status', __('状态'))->states($states1);

        return $form;
    }
}
