<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchCloudDividend extends BatchAction
{
    public $name = '批量下架';
    protected $selector = '.cloud-dividend';

    public function handle(Collection $collection, Request $request)
    {
        // 获取到表单中的`issue`值
        //$request->get('cloud_dividends');

        foreach ($collection as $model) {
            $model->update(['on_sale'=>0]);
        }

        return $this->response()->success('已下架')->refresh();
    }

    public function form()
    {
        //0-否  1-A 2-B 3-C
        $options = [
            0 => '不参与分红',
            1 => 'A 分红池',
            2 => 'B 分红池',
            3 => 'C 分红池',
        ];

        $this->radio('cloud_dividends', '请选择云算力分红池')->options($options);
    }

    public function html()
    {
        return "<a class='cloud-dividend btn btn-sm btn-primary'> <i class='fa fa-info-circle'></i> 批量下架</a>";
    }
}
