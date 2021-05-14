<?php

namespace App\Admin\Controllers;

use App\Models\Designer;
use App\Models\ReserveInformation;
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
        //$grid->column('service_project', __('Service project'));
        //$grid->column('time', __('Time'));
        $grid->column('created_at', __('创建时间'));
        //$grid->column('updated_at', __('Updated at'));
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
        $show->field('time', __('可预约时间'));
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

        $form->select('designer_id',__('设计师'))->options(function ($id) {
            $user = Designer::find($id);

            if ($user) {
                return [$user->id => $user->name];
            }
        })->ajax('/admin/api/designer')->required();

        $form->text('service_project', __('服务项目'));
        $form->text('time', __('可预约时间'));

        return $form;
    }
}
