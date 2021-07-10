<?php

namespace App\Admin\Controllers;

use App\Models\Community;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Storage;

class CommunityController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '晒单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Community());
        $grid->filter(function ($filter) {
            $filter->like('user.nickname', __('用户昵称'));
            $filter->like('title', '标题');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'));
        $grid->column('user.nickname', __('用户昵称'));
        $grid->column('title', __('标题'))->limit(20);
        $states1 = [
            'on'  => ['value' => 0, 'text' => '未审核', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '已审核', 'color' => 'primary'],
        ];
        $grid->column('status', __('状态'))->switch($states1);
        $grid->column('created_at', __('创建时间'));
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
        $show = new Show(Community::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user.nickname', __('用户昵称'));
        $show->field('title', __('标题'));
        $show->field('content', __('内容'));
        /*$show->field('many_images', __('Many images'));
        $show->field('video', __('Video'));
        $show->field('status', __('Status'));*/
        $show->field('many_images', __('图片'))->unescape()->as(function ($content) {
            $images = '';
            if($content){
                foreach ($content as $k=>$value){
                    $image = Storage::disk('oss')->url($value);
                    $images = $images."<div style='margin-top: 25px;float: left; margin-right: 25px'>
                        <img src='{$image}'  width='200' height='200'/>
                        </div>";
                }
            }else{
                $images = '';
            }

            return $images;
        });
        $show->video_play_url()->unescape()->as(function ($video_url) {
            return "<video width='320' height='320' controls>
                <source src='{$video_url}' type='video/mp4'>
            </video>";
        });
        $show->field('status', __('状态'))->using(['1' => '已审核', '0' => '未审核']);
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
        $form = new Form(new Community());

        $states1 = [
            'on'  => ['value' => 0, 'text' => '未审核', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '已审核', 'color' => 'primary'],
        ];
        $form->switch('status', __('状态'))->states($states1);

        return $form;
    }
}
