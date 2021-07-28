<?php

namespace App\Admin\Controllers;

use App\Models\Designer;
use App\Models\Production;
use App\Models\ProductionAge;
use App\Models\ProductionColor;
use App\Models\ProductionLength;
use App\Models\ProductionStyle;
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
        $grid->column('is_recommend', __('是否推荐'))->display(function ($value) {
            $html = '';
            if ($value == 1) {
                $html .= "<span class='label label-success' style='margin-left: 10px'>是</span>";
            }else{
                $html .= "<span class='label label-success' style='margin-left: 10px'>否</span>";
            }
            return $html; // 标题添加strong标签
        });
        /*$states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $grid->column('is_recommend', __('是否推荐'))->switch($states1);*/
        $grid->column('created_at', __('创建时间'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            //$actions->disableDelete();
        });
        /*$grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });*/
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
        $form->radio('type',__('作品类型'))->options(['0' => '视频', '1' => '图文'])->default('0')->required();
        $form->text('title', __('标题'))->required();
        $form->image('thumb', __('封面图片'))->rules('image')->move('images/articleimage')->uniqueName()->help('图片参考尺寸至少 108*108 比例1:1')->required();

        $form->radio('is_recommend','是否推荐')->options([
            0 => '否',
            1 => '是'
        ])->when(1,function (Form $form){
            $form->image('rectangle_image','封面长图')->rules('image')->uniqueName()->help('如果需要推荐到首页，需要上传封面长图，前端首页右边的长方形图片参考尺寸 175*75 比例 7:3，前端首页左边的正方形的图片参考尺寸还是108*108');
        })->required()->help('如果选择 是，下面的封面长图必须填上，否则页面报错');

        //$form->image('rectangle_image','封面长图')->rules('image')->uniqueName()->help('如果需要推荐到首页，需要上传封面长图，前端首页右边的长方形图片参考尺寸 175*75 比例 7:3，前端首页左边的正方形的图片参考尺寸还是108*108');
        //$form->multipleImage('many_images','多图上传')->uniqueName()->removable()->help('图片尺寸 375*668');
        $form->file('video', __('视频'))->move('files/articlevideo')->uniqueName()->help('选择视频类型，只需在此处添加视频即可');// 使用随机生成文件名 (md5(uniqid()).extension)
        $form->textarea('description', __('描述'));
        $form->editor('content', __('图文类型的内容'))->help('选择图文类型，只需在此处添加图片和视频即可');
        //$form->number('rating', __('浏览次数'))->default(0);
        /*$states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $form->switch('is_recommend', __('是否推荐'))->states($states1);*/
        $form->select('gender','性别')->options([
            '0'=>'男',
            '1'=> '女'
        ])->default(0);
        $form->select('age_id','年龄段')->options(ProductionAge::all()->pluck('name','id'));
        $form->select('length_id','长度')->options(ProductionLength::all()->pluck('name','id'));
        $form->select('color_id','色系')->options(ProductionColor::all()->pluck('name','id'));
        $form->multipleSelect('style_id','风格')->options(ProductionStyle::all()->pluck('name','id'));
        $form->saved(function (Form $form) {
            if($form->model()->is_recommend == 0){
                    $production = Production::find($form->model()->id);

                    $production->update([
                        'rectangle_image' => null,
                    ]);
                }
        });
        return $form;
    }
}
