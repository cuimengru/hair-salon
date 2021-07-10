<?php

namespace App\Admin\Controllers;

use App\Admin\Selectable\Users;
use App\Models\BalanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BalanceRecordController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '充值管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new BalanceRecord());

        $grid->filter(function ($filter) {
            $filter->like('user.nickname', __('用户昵称'));
            $filter->like('user.phone', __('用户手机号'));
            $filter->in('payment_method','充值方式')->checkbox([
                '1'    => '后台充值',
                '2'    => '支付宝',
                '3'    => '微信',
            ]);
            $filter->between('created_at','创建时间')->datetime();
        });

        $grid->column('id', __('Id'));
        $grid->column('user.nickname', __('用户昵称'));
        $grid->column('user.phone', __('用户手机号'));
        $grid->column('total_amount', __('充值金额'));
        //$grid->column('paid_at', __('Paid at'));
        $grid->column('payment_method', __('充值方式'))->display(function($value) {
            return BalanceRecord::$paymentMethodMap[$value];
        });
        //$grid->column('payment_no', __('Payment no'));

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
        $show = new Show(BalanceRecord::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user.nickname', __('用户昵称'));
        $show->field('user.phone', __('用户手机号'));
        $show->field('original_balance',__('充值前的金额'));
        $show->field('total_amount', __('充值金额'));
        $show->field('user.balance', __('充值后金额'));
        $show->field('paid_at', __('充值时间'));
        $show->field('payment_method', __('充值方式'))->using(['1' => '后台充值', '2' => '支付宝','3'=>'微信']);;
        $show->field('payment_no', __('支付平台订单号'));
        $show->field('admin_id', __('管理员'))->unescape()->as(function ($value) {
            if($value){
                $admin = DB::table('admin_users')->where('id','=',$value)->first();
                return $admin->name;
            }
        });

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
        $form = new Form(new BalanceRecord());

        $form->belongsTo('user_id', Users::class,'用户信息')->required();
        //$form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        //$form->number('payment_method', __('Payment method'));
        //$form->text('payment_no', __('Payment no'));
        //$form->text('user.balace',__('余额'));
        $form->decimal('total_amount', __('充值金额'));
        $form->select('admin_id',__('管理员'))->options(DB::table('admin_users')->pluck('name','id'))->required();
        $form->hidden('payment_method')->default(1);
        $form->hidden('original_balance');
        $form->hidden('paid_at');
        $form->saving(function (Form $form) {

            $user = User::where('id','=',$form->user_id)->first();
            $form->original_balance = $user->balance; //原始金额
            $form->paid_at = Carbon::now('Asia/shanghai'); //原始金额
            $user->update([
                'balance' => $form->total_amount + $user->balance,
            ]);
        });
        return $form;
    }
}
