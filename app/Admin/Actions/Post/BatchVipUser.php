<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchVipUser extends BatchAction
{
    public $name = '绑定贵宾卡';

    public function handle(Collection $collection,Request $request)
    {
        // 获取到表单中的`issue`值
        $request->get('vip_coding');
        $request->get('vip_balance');

        foreach ($collection as $model) {
            $model->update(['vip_coding'=> $request->get('vip_coding'),]);
        }

        return $this->response()->success('Success message...')->refresh();
    }

    public function html()
    {
        return "<a class='cloud-dividend btn btn-sm btn-primary'> <i class='fa fa-info-circle'></i> 绑定贵宾卡</a>";
    }

}
