<?php

namespace App\Admin\Controllers;

use App\Models\Culture;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TrainCultureController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '培训类产品';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Culture());

        $grid->filter(function ($filter) {
            $filter->like('title', '标题');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'));
        $grid->column('title', __('标题'))->limit(20);
        $grid->column('thumb_url', __('封面图片'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:200px;max-height:60px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });
        $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $grid->column('is_recommend', __('是否推荐'))->switch($states1);
        $grid->column('created_at', __('创建时间'));
        $grid->model()->where('place_id', '=',2);
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
        $show = new Show(Culture::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('place_id', __('Place id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('content', __('Content'));
        $show->field('thumb', __('Thumb'));
        $show->field('video', __('Video'));
        $show->field('video_url', __('Video url'));
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
        $form = new Form(new Culture());

        $form->text('title', __('标题'))->required();
        $form->image('thumb', __('封面图片'))->rules('image')->move('images/articleimage')->uniqueName();
        $form->file('video', __('视频'))->move('files/articlevideo')->uniqueName();// 使用随机生成文件名 (md5(uniqid()).extension)

        $form->textarea('description', __('描述'));
        $form->editor('content', __('内容'))->required();
        //$form->text('video_url', __('Video url'));
        $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $form->switch('is_recommend', __('是否推荐'))->states($states1);
        $form->hidden('place_id')->default(2);

        return $form;
    }
}
