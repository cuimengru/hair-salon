<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchProductiSalelist extends BatchAction
{
    public $name = '2批量下架';

    protected $selector = '.production-onsale';
    public function handle(Collection $collection,Request $request)
    {
        // 获取到表单中的`issue`值
        $request->get('on_sale');

        foreach ($collection as $model) {
            $model->update(['on_sale'=> $request->get('on_sale')]);
        }

        return $this->response()->success('已下架')->refresh();
    }

    public function form()
    {
        //0-否  1-A 2-B 3-C
        $options = [
            0 => '下架',
        ];

//        hyh增加default
        $this->radio('on_sale', '下架')->options($options)->default(0);
    }

    public function html()
    {
        return "<a class='production-onsale btn btn-sm btn-primary'> <i class='fa fa-info-circle'></i> 批量下架</a>";
    }

}
