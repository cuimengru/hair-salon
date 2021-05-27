<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\SelfCategory;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;

class SelfCategoryController extends AdminController
{
    protected $title = '自营类商品类目';

    public function index(Content $content)
    {
        return $content->title('自营类商品类目')
            ->description('列表')
            ->row(function (Row $row) {
                // 显示分类树状图
                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_url('self_categories'));
                    $form->select('parent_id', __('父类目'))->options(SelfCategory::selectOptions());
                    $form->text('name', __('名称'))->required();
                    $form->multipleImage('many_images','多图上传')->uniqueName()->removable();
                    $form->number('order', __('排序'))->default(0)->help('越小越靠前');
                    // 定义一个名为『是否目录』的单选框
                    $form->radio('is_directory', '是否目录')
                        ->options(['1' => '是', '0' => '否'])
                        ->default('0')
                        ->rules('required');
                    $form->hidden('_token')->default(csrf_token());
                    $column->append((new Box(__('新增自营类商品类目'), $form))->style('success'));
                });
            });
    }
    /**
     * 树状视图
     * @return Tree
     */
    protected function treeView()
    {
        return SelfCategory::tree(function (Tree $tree) {
            $tree->disableCreate(); // 关闭新增按钮
            $tree->branch(function ($branch) {
                return "<strong>{$branch['name']}</strong>"; // 标题添加strong标签
            });
        });
    }
    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return redirect()->route('self_categories.edit', ['id' => $id]);
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content->title(__('自营类商品类目'))
            ->description(__('编辑'))
            ->row($this->form()->edit($id));
    }
    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($isEditing = false)
    {
        $form = new Form(new SelfCategory());

        $form->display('id', 'ID');
        $form->select('parent_id', __('父类目'))->options(Category::selectOptions());
        $form->text('name', '类目名称')->rules('required');
        $form->multipleImage('many_images','多图上传')->uniqueName()->removable();
        $form->number('order', __('排序'))->default(0)->help('越小越靠前');
        $form->display('is_directory', '是否目录')->with(function ($value) {
            return $value ? '是' :'否';
        });
        return $form;
    }
}
