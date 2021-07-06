<?php

namespace App\Admin\Controllers;

use App\Models\SensitiveWord;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Storage;

class SensitiveWordController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '敏感词';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SensitiveWord());

        $grid->column('id', __('Id'));
        $grid->column('word', __('敏感词'));
        $grid->column('created_at', __('创建时间'));
        $grid->column('updated_at', __('更新时间'));
        $grid->filter(function (Grid\Filter $filter) {
            $filter->disableIdFilter();
            $filter->like('word','敏感词');
        });
        $grid->actions(function ($actions) {
            $actions->disableView();// 去掉查看
        });
        $grid->disableExport();// 禁用导出
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->model()->orderBy('id', 'desc'); // 排序
        $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
            $create->text('word', '敏感词');
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
        $show = new Show(SensitiveWord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('word', __('Word'));
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
        $form = new Form(new SensitiveWord());

        $form->display('id');
        $form->text('word', __('敏感词'))
            ->creationRules(['required', "unique:sensitive_words"])
            ->updateRules(['required', "unique:sensitive_words,word,{{id}}"]);
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();// 去掉`删除`按钮
            $tools->disableView();// 去掉`查看`按钮
        });

        $form->saved(function (Form $form) {
            //获取所有词 每行一个写入到文本 存储到S3
            $words = SensitiveWord::all();
            $words = collect($words)->pluck('word')->flatten()->toArray();
            $str = implode("\n", $words);
            Storage::disk('oss')->put('dict/words.txt', $str, 'public');//上传到public
            Storage::disk('dict')->put('dict/words.txt', $str, 'public');//写入本地
        });

        return $form;
    }
}
