<?php


namespace App\Admin\Selectable;

use App\Models\User;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Grid\Selectable;

class VipUsers extends Selectable
{
    public $model = User::class;

    public function make()
    {
        $this->column('id');
        $this->column('nickname',__('用户昵称'));
        $this->column('phone',__('手机号'));
        $this->column('vip_coding',__('贵宾卡编码'));
        $this->column('vip_balance',__('贵宾卡原始余额'));
        $this->column('type',__('用户类型'))->display(function($value) {
            if($value == 1){
                return '线下';
            }else{
                return '线上';
            }
        });
        $this->model()->where('is_binding', '=',1);
        //$this->column('created_at');

        $this->filter(function (Filter $filter) {
            $filter->like('nickname',__('用户昵称'));
            $filter->like('phone',__('手机号'));
        });
    }
}
