<?php

namespace App\Admin\Controllers;

use App\Admin\Selectable\VipUsers;
use App\Models\User;
use App\Models\VipRecord;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VipRecordController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '贵宾卡充值';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new VipRecord());
        $grid->filter(function ($filter) {
            $filter->like('user.nickname', __('用户昵称'));
            $filter->like('user.phone', __('用户手机号'));
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'));
        $grid->column('user.nickname', __('用户昵称'));
        $grid->column('user.phone', __('用户手机号'));
        $grid->column('total_amount', __('充值金额'));
        $grid->column('created_at', __('创建时间'));
        //$grid->column('updated_at', __('Updated at'));
        $grid->model()->orderBy('id', 'desc');

        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
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
        $show = new Show(VipRecord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user.nickname', __('用户昵称'));
        $show->field('user.phone', __('用户手机号'));
        $show->field('original_balance',__('充值前的金额'));
        $show->field('total_amount', __('充值金额'));
        $show->field('vip_balance', __('充值后金额'));
        $show->field('payment_method', __('充值方式'))->using(['1' => '后台充值']);;
        $show->field('admin_id', __('管理员'))->unescape()->as(function ($value) {
            if($value){
                $admin = DB::table('admin_users')->where('id','=',$value)->first();
                return $admin->name;
            }
        });
        $show->field('remark', __('备注'));
        $show->field('created_at', __('创建时间'));
        //$show->field('updated_at', __('Updated at'));
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
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
        $form = new Form(new VipRecord());

        $admin_user = Auth::guard('admin')->user();
        $form->belongsTo('user_id', VipUsers::class,'用户信息')->required();
        $form->decimal('total_amount', __('充值金额'))->help('支持正数和负数');
        $form->textarea('remark',__('备注'));
        $form->select('admin_id',__('管理员'))->options(DB::table('admin_users')->where('id','=',$admin_user->id)->pluck('name','id'))->required();
        $form->hidden('payment_method')->default(1);
        $form->hidden('original_balance');
        $form->hidden('paid_at');
        $form->saving(function (Form $form) {

            $user = User::where('id','=',$form->user_id)->first();
            $form->original_balance = $user->vip_balance; //原始金额
            $form->paid_at = Carbon::now('Asia/shanghai'); //原始金额
            $user->update([
                'vip_balance' => $form->total_amount + $user->vip_balance,
                'viporiginal_balance' => $form->total_amount + $user->vip_balance,
            ]);
        });
        return $form;
    }
}
