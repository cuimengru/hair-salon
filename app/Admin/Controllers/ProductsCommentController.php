<?php

namespace App\Admin\Controllers;

use App\Models\Comment;
use App\Models\ProductSku;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Storage;

class ProductsCommentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品评价';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Comment());
        $grid->filter(function ($filter) {
            $filter->like('user.nickname', __('用户'));
            $filter->like('order.no', '订单编号');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'))->sortable();
        //$grid->column('type', __('Type'));
        $grid->column('order.no', __('订单编号'));
        $grid->column('user.nickname', __('用户'));
        $grid->column('product.title', __('商品'));
        $grid->column('product_sku_id', __('SKU名称'))->display(function ($value) {
            $product_sku = ProductSku::where('id','=',$value)->first();
           return $product_sku['title'];
        });
        $grid->column('rate', __('评分'));
        $states1 = [
            'on'  => ['value' => 0, 'text' => '未审核', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '已审核', 'color' => 'primary'],
        ];
        $grid->column('status', __('状态'))->switch($states1);
        $grid->column('created_at', __('创建时间'));
        $grid->model()->where('type', '=',2);
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            //$actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
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
        $show = new Show(Comment::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('order.no', __('订单编号'));
        $show->field('user.nickname', __('用户'));
        $show->field('product.title', __('商品'));
        $show->field('product_sku_id', __('SKU名称'))->as(function ($value) {
            $product_sku = ProductSku::where('id','=',$value)->first();
            return $product_sku['title'];
        });
        $show->field('rate', __('评分'));
        $show->field('render_content', __('评论内容'));
        //$show->field('render_image', __('Render image'));
        $show->field('render_image', __('图片'))->unescape()->as(function ($content) {
            $images = '';
            if($content){
                foreach ($content as $k=>$value){
                    $image = Storage::disk('oss')->url($value);
                    $images = $images."<div style='margin-top: 25px;float: left; margin-right: 15px'>
                        <img src='{$image}'  width='100%'/>
                        </div>";
                }
            }else{
                $images = '';
            }

            return $images;
        });
        $show->field('video_url', __('视频'))->unescape()->as(function ($video_url) {
            return "<video width='320' height='320' controls>
                <source src='{$video_url}' type='video/mp4'>
            </video>";
        });
        //$show->field('render_video', __('Render video'));
        $show->field('status', __('状态'))->using(['1' => '已审核', '0' => '未审核']);
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));
        $show->panel()
            ->tools(function ($tools) {
                //$tools->disableEdit();
                //$tools->disableList();
                $tools->disableDelete();
            });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Comment());

        $states1 = [
            'on'  => ['value' => 0, 'text' => '未审核', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '已审核', 'color' => 'primary'],
        ];
        $form->switch('status', __('状态'))->states($states1);

        return $form;
    }
}
