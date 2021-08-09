<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchProductionSalelist extends BatchAction
{
    public $name = '批量上架';

    protected $selector = '.production-sale';
    public function handle(Collection $collection,Request $request)
    {
        // 获取到表单中的`issue`值
        $request->get('on_sale');

        foreach ($collection as $model) {
            $model->update(['on_sale'=> $request->get('on_sale')]);
        }

        return $this->response()->success('已上架')->refresh();
    }

    public function form()
    {
        //0-否  1-A 2-B 3-C
        $options = [
            1 => '上架',
        ];
//        hyh增加default
        $this->radio('on_sale', '上架')->options($options)->default(1);
    }

    public function html()
    {
        return "<a class='production-sale btn btn-sm btn-primary'> <i class='fa fa-info-circle'></i> 批量上架</a>";
    }

}
