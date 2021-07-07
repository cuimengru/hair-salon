<?php

namespace App\Admin\Controllers;

use App\Models\Advert;
use App\Models\AdvertCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AdvertController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '广告';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Advert());

        $grid->column('id', __('Id'))->sortable();
        $grid->column('category.name', __('广告位置'));
        $grid->column('title', __('标题'));
        $grid->column('thumb_url', __('图片'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:100px;max-height:60px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });

        $grid->column('created_at', __('创建时间'));
        $grid->actions(function ($actions) {
            //$actions->disableDelete();
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
        $show = new Show(Advert::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('thumb', __('Thumb'));
        $show->field('url', __('Url'));
        $show->field('order', __('Order'));
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
        $form = new Form(new Advert());

        $form->select('category_id',__('广告位置'))->options(function ($id) {
            $user = AdvertCategory::find($id);

            if ($user) {
                return [$user->id => $user->name];
            }
        })->ajax('/admin/api/advert_categories')->required();
        $form->text('title', __('标题'))->required();
        $form->image('thumb', __('图片'))->uniqueName()->required()->help('图片尺寸 345*136');
        $form->textarea('description', __('描述'));
        $form->editor('content', __('内容'))->required();
        $form->url('url', __('跳转链接'));
        $form->number('order', __('排序'))->default(0)->help('越小越靠前');
        //$form->radio('is_recommend', '是否推荐')->options(['1' => '是', '0'=> '否'])->default('0');

        return $form;
    }
}
