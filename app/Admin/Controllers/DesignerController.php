<?php

namespace App\Admin\Controllers;

use App\Models\Designer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class DesignerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '设计师';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Designer());
        $grid->filter(function ($filter) {
            $filter->like('name', '姓名');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'))->sortable();
        $grid->column('name', __('姓名'));
        $grid->column('thumb_url', __('封面图片'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:50px;max-height:50px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });
        $grid->column('position', __('职位'));
        $grid->column('rating', __('评价数量'));
        $grid->column('created_at', __('创建时间'));
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
        $show = new Show(Designer::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('姓名'));
        $show->field('thumb_url', __('封面图片'))->image();
        $show->field('position', __('职位'));
        $show->field('rating', __('评价数量'));
        $show->field('description', __('描述'));
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
        $form = new Form(new Designer());

        $form->text('name', __('姓名'))->required();
        $form->image('thumb', __('封面图片'))->required();
        $form->textarea('description', __('描述'));
        $form->text('position', __('职位'));
        $form->number('rating', __('评价数量'))->default(0);

        return $form;
    }

    public function apiIndex(Request $request)
    {
        $q = $request->get('q');

        return Designer::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }
}
