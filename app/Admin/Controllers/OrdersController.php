<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class OrdersController extends AdminController
{
    use ValidatesRequests;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order());

        $grid->filter(function ($filter) {
            $filter->like('user.nickname', '用户昵称');
            $filter->like('user.phone', '用户手机号');
            $filter->like('no', '订单流水号');
            $filter->in('ship_status','物流')->checkbox([
                '1'    => '未发货',
                '2'    => '已发货',
                '3'    => '已收货',
            ]);
            $filter->in('payment_method','支付方式')->checkbox([
                '1'    => '余额',
                '2'    => '支付宝',
                '3'    => '微信',
            ]);
            $filter->between('paid_at','支付时间')->datetime();
        });

        $grid->column('id', __('Id'));
        $grid->column('no', __('订单流水号'));
        $grid->column('user.nickname', __('买家'));
        $grid->column('total_amount', __('总金额'));
        $grid->column('paid_at', __('支付时间'));
        $grid->ship_status('物流')->display(function($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->status('订单状态')->display(function($value) {
            return Order::$statusMap[$value];
        });
        $grid->refund_status('退款状态')->display(function($value) {
            if($value == null){
                return '';
            }else{
                return Order::$refundStatusMap[$value];
            }
        });
        // 只展示已支付的订单，并且默认按支付时间倒序排序
        $grid->model()->orderBy('paid_at', 'desc');
        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            //$actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('查看订单')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.orders.show', ['order' => Order::find($id)]));
    }

    /*public function ship(Order $order,Request $request)
    {
        //判断当前订单是否已支付
        if($order->paid_at){
            throw new InvalidRequestException('该订单未付款');
        }
        // 判断当前订单发货状态是否为未发货
        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已发货');
        }

        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no'      => ['required'],
        ], [], [
            'express_company' => '物流公司',
            'express_no'      => '物流单号',
        ]);

        // 将订单发货状态改为已发货，并存入物流信息
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            // 我们在 Order 模型的 $casts 属性里指明了 ship_data 是一个数组
            // 因此这里可以直接把数组传过去
            'ship_data'   => $data,
        ]);

        // 返回上一页
        return redirect()->back();
    }*/

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
   /* protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('no', __('No'));
        $show->field('user_id', __('User id'));
        $show->field('address', __('Address'));
        $show->field('total_amount', __('Total amount'));
        $show->field('remark', __('Remark'));
        $show->field('paid_at', __('Paid at'));
        $show->field('payment_method', __('Payment method'));
        $show->field('payment_no', __('Payment no'));
        $show->field('status', __('Status'));
        $show->field('refund_no', __('Refund no'));
        $show->field('closed', __('Closed'));
        $show->field('reviewed', __('Reviewed'));
        $show->field('ship_status', __('Ship status'));
        $show->field('ship_data', __('Ship data'));
        $show->field('extra', __('Extra'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }*/

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order());

        $form->text('no', __('订单流水号'))->readonly();
        $form->radioCard('status', __('订单状态'))
            ->options([
                '1'=>Order::$statusMap['1'],
                '2'=>Order::$statusMap['2'],
                '3'=>Order::$statusMap['3'],
            ])->readonly();
        $form->radioCard('ship_status', __('物流状态'))
            ->options([
                '1'=>Order::$shipStatusMap['1'],
                '2'=>Order::$shipStatusMap['2'],
                '3'=>Order::$shipStatusMap['3'],
            ])->required();
        $form->embeds('ship_data','物流数据', function ($form) {
            $form->text('express_company',__('物流公司'))->rules('required');
            $form->text('express_no',__('物流单号'))->rules('required');
        });
        $form->radioCard('refund_status', __('退款状态'))
            ->options([
                //'6'=>Order::$refundStatusMap['6'],
                '7'=>Order::$refundStatusMap['7'],
                '8'=>Order::$refundStatusMap['8'],
                '9'=>Order::$refundStatusMap['9'],
            ])->help('直接退款到用户余额');
        $form->embeds('extra','退款理由', function ($form) {
            $form->text('refund_reason',__('理由'))->readOnly()->help('退款图片请查看订单详情');
        });
        $form->radioCard('refund_status', __('是否同意退款'))
            ->options([
                '8'=>'同意',
                '9'=>'不同意',
            ]);
        $form->embeds('extra','拒绝退款', function ($form) {
            $form->text('disagree_reason',__('理由'));
        });
        $form->saved(function (Form $form) {
            if($form->model()->refund_status==8){
                $order = Order::find($form->model()->id);
                //退款到用户余额
                $user = User::find($order->user_id);
                $user->balance = $user->balance + $order->total_amount;
                $user->save();
            }
        });
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete();
        });
        return $form;
    }
}
