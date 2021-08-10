<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchProduction;
use App\Admin\Actions\Post\BatchProductionSalelist;
use App\Admin\Actions\Post\BatchProductiSalelist;
use App\Models\Designer;
use App\Models\Production;
use App\Models\ProductionAge;
use App\Models\ProductionColor;
use App\Models\ProductionFace;
use App\Models\ProductionHair;
use App\Models\ProductionHeight;
use App\Models\ProductionLength;
use App\Models\ProductionProject;
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
            //hyh增加筛选
            $filter->equal('is_recommend', __('是否推荐'))->radio([
                0 => '不推荐',
                1 => '推荐',
            ]);
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
        $states = [
            'on'  => ['value' => 0, 'text' => '否', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '是', 'color' => 'primary'],
        ];
        $grid->column('on_sale', __('是否上架'))->switch($states);
        /*$grid->column('is_recommend', __('是否推荐'))->display(function ($value) {
            $html = '';
            if ($value == 1) {
                $html .= "<span class='label label-success' style='margin-left: 10px'>是</span>";
            }else{
                $html .= "<span class='label label-success' style='margin-left: 10px'>否</span>";
            }
            return $html; // 标题添加strong标签
        });*/
       /* $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $grid->column('is_recommend', __('是否推荐'))->switch($states1);*/
        $grid->column('is_recommend', __('是否推荐'))->radio([
            0 => '不推荐',
            1 => '推荐',
        ]);
        $grid->column('gender',__('性别'))->display(function ($value){
            if ($value == 0){
                return '男';
            }else{
                return '女';
            }
        });
//        hyh身高改多选
        $grid->column('height_id', __('身高'))->display(function ($service_project) {
            $html = '';
            if(!empty($service_project)){
                foreach ($service_project as $k => $value){
                    $service = ProductionHeight::where('id','=',$value)->select('name')->first();
                    if($service){
                        $html .= "<span class='label label-success' style='margin-left: 8px'>{$service['name']}</span>";
                    }
                }
                return $html;
            }else{
                return $html;
            }

        });

        $grid->column('age_id', __('年龄段'))->display(function ($service_project) {
            $html = '';
            if(!empty($service_project)){
                foreach ($service_project as $k => $value){
                    $service = ProductionAge::where('id','=',$value)->select('name')->first();
                    if($service){
                        $html .= "<span class='label label-success' style='margin-left: 8px'>{$service['name']}</span>";
                    }
                }
                return $html;
            }else{
                return $html;
            }

        });
//        $grid->column('color.name', __('发质'));
        $grid->column('color_id', __('发质'))->display(function ($service_project) {
            $html = '';
            if(!empty($service_project)){
                foreach ($service_project as $k => $value){
                    $service = ProductionColor::where('id','=',$value)->select('name')->first();
                    if($service){
                        $html .= "<span class='label label-success' style='margin-left: 8px'>{$service['name']}</span>";
                    }
                }
                return $html;
            }else{
                return $html;
            }
        });

//        $grid->column('length.name', __('长度'));
        $grid->column('length_id', __('长度'))->display(function ($service_project) {
            $html = '';
            if(!empty($service_project)){
                foreach ($service_project as $k => $value){
                    $service = ProductionLength::where('id','=',$value)->select('name')->first();
                    if($service){
                        $html .= "<span class='label label-success' style='margin-left: 8px'>{$service['name']}</span>";
                    }
                }
                return $html;
            }else{
                return $html;
            }
        });

//        $grid->column('face.name', __('脸型'));
        $grid->column('face_id', __('脸型'))->display(function ($service_project) {
            $html = '';
            if(!empty($service_project)){
                foreach ($service_project as $k => $value){
                    $service = ProductionFace::where('id','=',$value)->select('name')->first();
                    if($service){
                        $html .= "<span class='label label-success' style='margin-left: 8px'>{$service['name']}</span>";
                    }
                }
                return $html;
            }else{
                return $html;
            }
        });

        $grid->column('style_id', __('风格'))->display(function ($service_project) {
            $html = '';
            if(!empty($service_project)){
                foreach ($service_project as $k => $value){
                    $service = ProductionStyle::where('id','=',$value)->select('name')->first();
                    if($service){
                        $html .= "<span class='label label-success' style='margin-left: 8px'>{$service['name']}</span>";
                    }
                }
                return $html;
            }else{
                return $html;
            }

        });
//        $grid->column('project.name', __('项目'));
        $grid->column('project_id', __('项目'))->display(function ($service_project) {
            $html = '';
            if(!empty($service_project)){
                foreach ($service_project as $k => $value){
                    $service = ProductionProject::where('id','=',$value)->select('name')->first();
                    if($service){
                        $html .= "<span class='label label-success' style='margin-left: 8px'>{$service['name']}</span>";
                    }
                }
                return $html;
            }else{
                return $html;
            }
        });


        $grid->column('hair_id', __('烫染'))->display(function ($service_project) {
            $html = '';
            if(!empty($service_project)){
                foreach ($service_project as $k => $value){
                    $service = ProductionHair::where('id','=',$value)->select('name')->first();
                    if($service){
                        $html .= "<span class='label label-success' style='margin-left: 8px'>{$service['name']}</span>";
                    }
                }
                return $html;
            }else{
                return $html;
            }

        });
        $grid->column('created_at', __('创建时间'));
        $grid->actions(function ($actions) {
            $actions->disableView();
            //$actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            /*$tools->batch(function ($batch) {
                $batch->disableDelete();
            });*/
            $tools->append(new BatchProductionSalelist());
            $tools->append(new BatchProductiSalelist());
            $tools->append(new BatchProduction());
        });
        $grid->model()->orderBy('sort_list', 'desc');//hyh作品排序
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
//        $form->text('title', __('标题'))->required();
        $form->text('title', __('标题'));//hyh作品标题改为非必填


        $form->image('thumb', __('封面图片'))->rules('image')->move('images/articleimage')->uniqueName()->help('图片参考尺寸至少 108*108 比例1:1')->required();
        $states = [
            'on'  => ['value' => 0, 'text' => '否', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '是', 'color' => 'primary'],
        ];

//        hyh作品排序
        $form->number('sort_list', __('排序'))->help('请填写数字 数字越大越靠前');

        $form->switch('on_sale', __('是否上架'))->states($states);
        $form->radio('is_recommend','是否推荐')->options([
            0 => '否',
            1 => '是'
        ])->when(1,function (Form $form){

//      hyh推荐作品排序
            $form->number('sort', __('排序'))->help('请填写数字 数字越大越靠前');

            $form->image('rectangle_image','封面长图')->rules('image')->uniqueName()->help('如果需要推荐到首页，需要上传封面长图，前端首页右边的长方形图片参考尺寸 175*75 比例 7:3，前端首页左边的正方形的图片参考尺寸还是108*108');

        })
          ->when(0,function (Form $form){
          //hyh推荐作品排序 选择“否”的时候，不显数字示表单
//        $form->number('sort', __('排序'))->default(0)->help('');
        })->help('如果选择“是”，下面的排序字段不能为空，否则页面报错');
//       如果推荐=1，那么sort字段为后台填写的数字或者默认为0
//       如果推荐=0，那么sort字段设置为0
//       $form->sort==0 实际上是为后台列表页处选择更改推荐而写的
        $form->saving(function (Form $form) {
            ($form->is_recommend==0 || $form->sort==0)?$hyh=0:$hyh=$form->sort;
            $form->sort = $hyh;
        });


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
//      $form->select('height_id','身高')->options(ProductionHeight::all()->pluck('name','id')); //hyh身高改多选
        $form->multipleSelect('height_id','身高')->options(ProductionHeight::all()->pluck('name','id'));
        $form->multipleSelect('age_id','年龄段')->options(ProductionAge::all()->pluck('name','id'));
        $form->multipleSelect('color_id','发质')->options(ProductionColor::all()->pluck('name','id'));
        $form->multipleSelect('length_id','长度')->options(ProductionLength::all()->pluck('name','id'));
        $form->multipleSelect('face_id','脸型')->options(ProductionFace::all()->pluck('name','id'));
        $form->multipleSelect('style_id','风格')->options(ProductionStyle::all()->pluck('name','id'));
        $form->multipleSelect('project_id','项目')->options(ProductionProject::all()->pluck('name','id'));
        $form->multipleSelect('hair_id','烫染')->options(ProductionHair::all()->pluck('name','id'));
        $form->tools(function (Form\Tools $tools) {
            // 去掉`查看`按钮
            $tools->disableView();
        });
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
