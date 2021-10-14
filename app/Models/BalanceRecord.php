<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceRecord extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    const PAYMENT_METHOD_BANLANCE = 1;
    const PAYMENT_METHOD_ALIPAY = 2;
    const PAYMENT_METHOD_WECHAT = 3;
    const PAYMENT_METHOD_MINI = 5;

    public static $paymentMethodMap = [
        self::PAYMENT_METHOD_BANLANCE  => '后台充值',
        self::PAYMENT_METHOD_ALIPAY => '支付宝',
        self::PAYMENT_METHOD_WECHAT  => '微信',
        self::PAYMENT_METHOD_MINI  => '小程序',
    ];

    protected $fillable = [
        'user_id', 'paid_at','payment_method', 'payment_no', 'total_amount','admin_id','original_balance','no','remark'
    ];

    protected $dates = [
        'paid_at',
    ];

    protected $appends = [
        'top_balance'
    ];
    public function getTopBalanceAttribute()
    {
        $vip_balance = number_format($this->original_balance + $this->total_amount,2);

        return $vip_balance;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
