<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductLabel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '集品类商品';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product());
        $grid->filter(function ($filter) {
            $filter->like('category.name', '商品类目');
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
        $grid->model()->where('type', '=',1);
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

        $form->select('category_id', __('类目'))->options(Category::selectOptions());
        $form->text('title', __('商品名称'))->rules('required');
        $form->text('country','商品产地');
        $form->text('country_name','所属国家');
        $form->multipleSelect('label_id','商品标签')->options(ProductLabel::all()->pluck('name','id'));
        $form->image('image', __('封面图片'))->uniqueName()->rules('required|image');
        $form->multipleImage('many_image','多图上传')->uniqueName()->removable();
        $form->editor('description', __('商品描述'))->rules('required');
        $form->radio('on_sale', '上架')->options(['1' => '是', '0'=> '否'])->default('1')->required();
        $form->text('rating', __('评分'))->default(5.0);
        $form->decimal('price', __('商品现价'))->default(0.00);
        $form->decimal('original_price', __('商品原价'))->default(0.00);
        $form->radio('package_mail', '是否包邮')->options(['1' => '是', '0'=> '否'])->default(1)->required();
        $form->decimal('postage','邮费')->default(0)->required();
        $form->hidden('postage')->default(0)->required();
        $form->table('property', __('属性'), function ($table) {
            $table->text('property_name','属性名称');
            $table->text('property_content','属性内容');
        });
        $states1 = [
            'on'  => ['value' => 0, 'text' => '不推荐', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '推荐', 'color' => 'primary'],
        ];
        $form->switch('is_recommend', __('是否推荐'))->states($states1);
        // 直接添加一对多的关联模型
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->image('image','SKU图片')->required();
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.00');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });
        $form->hidden('type')->default(1);
        $form->tools(function (Form\Tools $tools) {
            //$tools->disableList();  // 去掉`列表`按钮
            $tools->disableDelete();  // 去掉`删除`按钮
            $tools->disableView();  // 去掉`查看`按钮
        });
        return $form;
    }
}
