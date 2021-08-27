<?php

namespace App\Admin\Controllers;

use App\Models\Designer;
use App\Models\ReserveInformation;
use App\Models\ServiceProject;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ReserveInformationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '预约信息';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ReserveInformation());

        $grid->filter(function ($filter) {
            $filter->like('designer.name', '设计师');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'));
        $grid->column('designer.name', __('设计师'));
        $grid->column('service_project', __('服务项目'))->display(function ($service_project) {
            $html = '';
            foreach ($service_project as $k => $value){
                $service = ServiceProject::where('id','=',$value)->select('name')->first();
                if($service){
                    $html .= "<span class='label label-success' style='margin-left: 10px'>{$service['name']}</span>";
                }
            }
            return $html;
        });
        //$grid->column('time', __('Time'));

        //hyh新增预约设计师列表排序
        $grid->column('sort', __('排序'));

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
        $show = new Show(ReserveInformation::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('designer.name', __('设计师'));
        $show->field('service_project', __('服务项目'));
        //$show->field('time', __('可预约时间'));
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
        $form = new Form(new ReserveInformation());

        $form->select('designer_id',__('设计师'))->options(Designer::all()->pluck('name', 'id'))->required();

        $form->multipleSelect('service_project', __('服务项目'))->options(ServiceProject::all()->pluck('service_name','id'));
        //$form->text('time', __('可预约时间'));

        //hyh新增预约设计师列表排序
        $form->number('sort', __('排序'))->help('请填写数字 数字越大越靠前【请与推荐的设计师排序保持一致】');

        return $form;
    }
}
