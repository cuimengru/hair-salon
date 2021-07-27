<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchVipUser extends BatchAction
{
    public $name = '绑定贵宾卡';
    protected $selector = '.vip-dividend';
    public function handle(Collection $collection,Request $request)
    {
        // 获取到表单中的`issue`值
        $request->get('vip_coding');
        $request->get('vip_balance');
        $request->get('is_binding');

        foreach ($collection as $model) {
            $model->update([
                'vip_coding'=> $request->get('vip_coding'),
                'vip_balance'=> $request->get('vip_balance'),
                'is_binding' => 1,
            ]);
        }

        return $this->response()->success('绑定成功')->refresh();
    }

    public function form()
    {
        $this->text('vip_coding', '贵宾卡编码');
        $this->text('vip_balance', '贵宾卡余额');
    }

    public function html()
    {
        return "<a class='vip-dividend btn btn-sm btn-primary'> <i class='fa fa-info-circle'></i> 绑定贵宾卡</a>";
    }

}
