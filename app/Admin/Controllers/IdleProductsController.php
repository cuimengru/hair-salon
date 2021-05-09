<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class IdleProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Product';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());
        $grid->filter(function ($filter) {
            $filter->like('category_id', '商品类目ID');
            $filter->like('title', '商品名称');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'))->sortable();
        $grid->column('category.name', __('商品类目'));
        $grid->column('title', __('商品名称'));
        $grid->on_sale('已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->price('价格');
        $grid->rating('评分');
        $grid->sold_count('销量');
        $grid->review_count('评论数');
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
        $grid->model()->where('type', '=',3);
        return $grid;

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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('category_id', __('Category id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
        $show->field('on_sale', __('On sale'));
        $show->field('rating', __('Rating'));
        $show->field('sold_count', __('Sold count'));
        $show->field('review_count', __('Review count'));
        $show->field('price', __('Price'));
        $show->field('original_price', __('Original price'));
        $show->field('type', __('Type'));
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
        $form = new Form(new Product());

        //$form->number('category_id', __('Category id'));
        $form->text('title', __('商品名称'))->rules('required');
        $form->image('image', __('封面图片'))->rules('required|image');
        $form->editor('description', __('商品描述'))->rules('required');
        $form->radio('on_sale', '上架')->options(['1' => '是', '0'=> '否'])->default('0');
        $form->text('rating', __('评分'))->default(5.0);
        $form->decimal('price', __('商品现价'))->default(0.00);
        $form->decimal('original_price', __('商品原价'))->default(0.00);
        $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $form->switch('is_recommend', __('是否推荐'))->states($states1);
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });
        $form->hidden('type')->default(3);
        $form->tools(function (Form\Tools $tools) {
            //$tools->disableList();  // 去掉`列表`按钮
            $tools->disableDelete();  // 去掉`删除`按钮
            $tools->disableView();  // 去掉`查看`按钮
        });

        return $form;
    }
}
