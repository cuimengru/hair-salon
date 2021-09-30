<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Leonis\Notifications\EasySms\Messages\EasySmsMessage;

class VerificationCodeAdmin extends Notification implements ShouldQueue
{
//hyh后台增加短信 新增的这个文件

    use Queueable;

    protected $code;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
//    public function __construct($code)
//    {
//        $this->code = $code;
//    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [EasySmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toEasySms($notifiable)
    {
        /*try {*/
            return (new EasySmsMessage)
//                ->setContent('已成功为您注册会员，初始密码为手机号后六位。如需下载APP请在各大应用商店搜索“锦之DO”即可，感谢您的支持！回T退订');
                ->setTemplate(config('easysms.aliyun_sms_template_admin'));
//                ->setData(['code' => $this->code]);

//                ->setData(['code' => $this->code]);
        /*}catch (\Exception $e) {

            Log::error(__METHOD__ . '|' . __METHOD__ . '执行失败', ['error' => $e]);
        }*/

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
