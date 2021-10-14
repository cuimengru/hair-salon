<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\BatchVipUser;
use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

//hyh后台增加短信 新增
use App\Notifications\VerificationCodeAdmin;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Overtrue\EasySms\PhoneNumber;
use App\Notifications\EmailVerify;
use Illuminate\Support\Facades\Notification;


class OfflineUserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '线下用户';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());
        $grid->filter(function ($filter) {
            //$filter->disableIdFilter();  // 去掉默认的id过滤器
            // 在这里添加字段过滤器
            //$filter->like('name', 'name');
            $filter->like('phone', '手机号');
            $filter->like('email', '邮箱');
            $filter->between('created_at','创建时间')->datetime();
        });
        $grid->column('id', __('Id'));
        $grid->column('avatar_url', __('头像'))->display(function ($value) {
            $icon = "";
            if ($value) {
                $icon = "<img src='$value' style='max-width:30px;max-height:30px;text-align: left' class='img'/>";
            }
            return $icon; // 标题添加strong标签
        });
        $grid->column('name', __('姓名'));
        $grid->column('nickname', __('昵称'));
        $grid->column('phone', __('手机号'));
        //$grid->column('email', __('邮箱'));
//        $grid->email_verified_at('已验证邮箱')->display(function ($value) {
//            return $value ? '是' : '否';
//        });
        $grid->status('审核状态')->radio([
            0 => '未审核',
            1 => '已审核',
            -1 => '审核中',
        ])->help('可编辑');
        $grid->column('is_binding', __('是否绑定贵宾卡'))->bool(['0' => false, '1' => true]);
        $grid->column('vip_balance', __('贵宾卡余额'))->display(function ($value){
            if ($value == 0){
                return '0';
            }else{
                return $value;
            }
        });
        $grid->column('created_at', __('创建时间'));
        //$grid->column('updated_at', __('更新时间'));
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            //$actions->disableEdit();// 去掉删除
        });
        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new BatchVipUser());
        });
        $grid->disableExport(); // 禁用导出数据
        * @param mixed $id
    * @return Show
        */
    protected function detail($id)
    {
        $grid->disableColumnSelector();// 禁用行选择器
        $grid->model()->orderBy('id', 'desc');
        $grid->model()->where('type', '=',1);
        return $grid;
    }

    /**
     * Make a show builder.
     *
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('avatar_url', __('头像'))->image();
        $show->field('name', __('姓名'));
        $show->field('nickname', __('昵称'));
        $show->field('phone', __('手机号'));
        //$show->field('email', __('邮箱'));
        //$show->field('email_verified_at', __('邮箱验证'));
        $show->field('introduce', __('简介'));
        $show->field('integral', __('积分'));
        $show->field('balance', __('余额'));
        $show->field('status', __('审核状态'))->using(['1' => '已审核', '0' => '未审核','-1'=>'审核中']);
        $show->field('created_at', __('创建时间'));
        $show->field('updated_at', __('更新时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
//        admin_toastr('测试');
        $form = new Form(new User());
        $form->image('avatar', __('头像'));
        $form->text('name', __('姓名'));
        $form->text('nickname', __('昵称'));
        if ($form->isEditing()) {
            $form->text('phone', __('手机号'));
            //$form->text('email', __('邮箱'));
            //$form->password('password', __('密码'))->help('不修改密码无需填写 默认密码 123456');
        }
        if ($form->isCreating()) {
            $form->mobile('phone', __('手机号'))->required();
            //$form->text('email', __('邮箱'))->required();
            $form->password('password', __('密码'))->default(bcrypt(123456))->required()->help('请设置为手机号后六位！！！ 默认密码 123456');
        }
        $form->textarea('introduce', __('简介'));
        $form->text('integral', __('积分'))->default(0.00);
        //$form->text('original_balance', __('原始余额'))->default(0.00);
        $form->text('balance', __('余额'))->default(0.00);
        $form->radioCard('status', __('审核状态'))->options(['0' => '未审核', '1' => '已审核','-1'=>'审核中'])->default('0');
        $form->hidden('type')->default(1);

//        hyh后台增加短信
        if ($form->isCreating()) {
            $form->saving(function (Form $form) {
//               file_put_contents("../1234hhh-phone.txt", var_export($form->phone,true));
                //$code='1234';

                $phone=$form->phone;
                try{
                Notification::route(
                    EasySmsChannel::class,
                    new PhoneNumber($phone)
                )->notify(new VerificationCodeAdmin());

                }catch (\Exception $e) {
                    file_put_contents("../sms-error.txt", var_export($e,true));
                }

//                SMS_225120534
//                已成功为您注册会员，初始密码为手机号后六位。如需下载APP请在各大应用商店搜索“锦之DO”即可，感谢您的支持！回T退订
            });
        }

        return $form;
    }
}
