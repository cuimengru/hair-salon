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

    public static $paymentMethodMap = [
        self::PAYMENT_METHOD_BANLANCE  => '后台充值',
        self::PAYMENT_METHOD_ALIPAY => '支付宝',
        self::PAYMENT_METHOD_WECHAT  => '微信',
    ];

    protected $fillable = [
        'user_id', 'paid_at','payment_method', 'payment_no', 'total_amount','admin_id','original_balance','no'
    ];

    protected $dates = [
        'paid_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
