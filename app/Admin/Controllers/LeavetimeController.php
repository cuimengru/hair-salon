<?php

namespace App\Admin\Controllers;

use App\Models\Designer;
use App\Models\Leavetime;
use App\Models\Worktime;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class LeavetimeController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '请假';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Leavetime());
        $grid->filter(function ($filter) {
            $filter->like('designer.name', '设计师');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'));
        $grid->column('designer.name', __('设计师'));
        $grid->column('type', __('请假类型'))->display(function ($value) {
            return $value ? '半天' : '全天';
        });
        $grid->column('date', __('请假日期'));
        $grid->column('time', __('时间段'))->display(function ($time) {
            $html = '';
            foreach ($time as $k => $value){
                $work = Worktime::where('id','=',$value)->first();
                if($work){
                    $html .= "<span class='label label-success' style='margin-left: 10px'>{$work['time']}</span>";
                }else{
                    $html = '';
                }

            }
            return $html;
        });
        $grid->column('created_at', __('创建日期'));
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
        $show = new Show(Leavetime::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('designer_id', __('Designer id'));
        $show->field('type', __('Type'));
        $show->field('date', __('Date'));
        $show->field('time', __('Time'));
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
        $form = new Form(new Leavetime());

        /*$form->select('designer_id',__('设计师'))->options(function ($id) {
            $designer = Designer::find($id);

            if ($designer) {
                return [$designer->id => $designer->name];
            }
        })->ajax('/admin/api/designer')->required();*/
        $form->select('designer_id',__('设计师'))->options(Designer::all()->pluck('name', 'id'))->required();
        $form->radio('type', __('请假类型'))->options(['0' => '全天', '1'=> '半天'])->default(0)
            ->required()->help('如果请假为半天请填写请假时间段, 全天无需填写时间段');
        $form->date('date', __('请假日期'))->default(date('Y-m-d'));
        $form->embeds('time',__('时间段'), function ($form) {
            $form->select('start_time', __('开始时间'))->options(Worktime::all()->pluck('time', 'id'));
            $form->select('end_time', __('结束时间'))->options(Worktime::all()->pluck('time', 'id'));

        });
        return $form;
    }
}
