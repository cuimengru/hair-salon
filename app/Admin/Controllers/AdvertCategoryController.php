<?php

namespace App\Admin\Controllers;

use App\Models\AdvertCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class AdvertCategoryController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '广告分类';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AdvertCategory());

        $grid->column('id', __('Id'));
        $grid->column('name', __('名称'));
        $grid->column('created_at', __('创建时间'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableView();
        });
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
        $show = new Show(AdvertCategory::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
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
        $form = new Form(new AdvertCategory());

        $form->text('id', __('Id'))->readonly();
        $form->text('name', __('Name'));

        return $form;
    }
    public function apiIndex(Request $request)
    {
        $q = $request->get('q');

        return AdvertCategory::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }
}
