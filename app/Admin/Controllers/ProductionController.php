<?php

namespace App\Admin\Controllers;

use App\Models\Designer;
use App\Models\Production;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '作品';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Production());
        $grid->filter(function ($filter) {
            $filter->like('title', '标题');
            $filter->between('created_at','创建时间')->datetime();
        });

        $grid->column('id', __('Id'))->sortable();
        //$grid->column('designer.name', __('设计师'));
        $grid->column('title', __('标题'))->limit(20);
        $grid->column('thumb_url', __('封面图片'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:50px;max-height:50px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });

        $grid->column('rating', __('浏览次数'));
        $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $grid->column('is_recommend', __('是否推荐'))->switch($states1);
        $grid->column('created_at', __('创建时间'));
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
        $show = new Show(Production::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('designer_id', __('Designer id'));
        $show->field('title', __('Title'));
        $show->field('thumb', __('Thumb'));
        $show->field('video', __('Video'));
        $show->field('description', __('Description'));
        $show->field('content', __('Content'));
        $show->field('rating', __('Rating'));
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
        $form = new Form(new Production());

        /*$form->select('designer_id',__('设计师'))->options(function ($id) {
            $designer = Designer::find($id);

            if ($designer) {
                return [$designer->id => $designer->name];
            }
        })->ajax('/admin/api/designer')->required();*/
        $form->text('title', __('标题'))->required();
        $form->image('thumb', __('封面图片'))->rules('image')->move('images/articleimage')->uniqueName()->help('图片尺寸 108*108');
        $form->multipleImage('many_images','多图上传')->uniqueName()->removable()->help('图片尺寸 375*668');
        $form->file('video', __('视频'))->move('files/articlevideo')->uniqueName();// 使用随机生成文件名 (md5(uniqid()).extension)
        $form->textarea('description', __('描述'));
        $form->editor('content', __('内容'));
        $form->radio('type',__('作品类型'))->options(['0' => '视频', '1' => '图文'])->default('0');
        //$form->number('rating', __('浏览次数'))->default(0);
        $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $form->switch('is_recommend', __('是否推荐'))->states($states1);

        return $form;
    }
}
