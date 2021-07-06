<?php

namespace App\Admin\Controllers;

use App\Models\Feedback;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Storage;

class FeedbackController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '问题反馈';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Feedback());
        $grid->filter(function ($filter) {
            $filter->like('user.nickname', '用户昵称');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'))->sortable();
        $grid->column('user.nickname', __('用户昵称'));
        $grid->column('content', __('内容'))->limit(20);
        $grid->actions(function ($actions) {
            //$actions->disableView();
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->disableCreateButton();
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
        $show = new Show(Feedback::findOrFail($id));
        $show->field('id', __('Id'));
        $show->field('user.name', __('用户姓名'));
        $show->field('content', __('内容'));
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

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Feedback());



        return $form;
    }
}
