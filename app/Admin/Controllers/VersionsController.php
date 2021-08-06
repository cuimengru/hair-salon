<?php

namespace App\Admin\Controllers;

use App\Models\Versions;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class VersionsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Versions());

        $grid->column('id', __('Id'));
       /* $grid->column('platform', __('平台'))->display(function ($value) {
            return Versions::$platformMap[$value];
        });*/
        $grid->column('version', __('版本号'));
        $grid->column('description', __('描述'))->limit(20);
        $grid->column('url', __('下载地址'));
        $states = [
            'on'  => ['value' => 0, 'text' => '不启用', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
        ];
        $grid->column('status',__('是否启用'))->switch($states);
        $grid->column('created_at', __('创建时间'));
        /*$grid->column('updated_at', __('Updated at'));
        $grid->column('deleted_at', __('Deleted at'));*/
        $grid->disableFilter();
        $grid->disableExport(); // 禁用导出数据
        $grid->disableColumnSelector();// 禁用行选择器

        $grid->model()->orderBy('id', 'desc');// 按照 ID 倒序

        $grid->actions(function ($actions) {
            $actions->disableDelete();// 去掉删除
            $actions->disableView();// 去掉查看
            //$actions->disableEdit();// 去掉编辑
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
        $show = new Show(Versions::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('platform', __('Platform'));
        $show->field('version', __('Version'));
        $show->field('description', __('Description'));
        $show->field('url', __('Url'));
        $show->field('status', __('Status'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Versions());

        //$form->radioCard('platform', __('平台'))->default(1)->options(['1' => 'Android', '2' => 'iOS'])->default('1')->required();
        $form->hidden('platform')->default(1);
        $form->text('version', __('Android 版本号'));
        $form->url('url', __('Android 下载地址'));
        $form->text('ios_version', __('iOS 版本号'));
        $form->url('ios_url', __('iOS 下载地址'))->default('https://apps.apple.com/cn/app/%E9%94%A6%E4%B9%8Bdo/id1575899926');
        $form->textarea('description', __('描述'))->required();
        $states = [
            'on'  => ['value' => 0, 'text' => '不启用', 'color' => 'default'],
            'off' => ['value' => 1, 'text' => '启用', 'color' => 'primary'],
        ];
        $form->switch('status', __('是否启用'))->states($states);

        //$form->switch('status', __('Status'));
        $form->tools(function (Form\Tools $tools) {
            //$tools->disableList();  // 去掉`列表`按钮
            $tools->disableDelete();  // 去掉`删除`按钮
            $tools->disableView();  // 去掉`查看`按钮
        });

        return $form;
    }
}
