<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    const STATUS_UNPAY = 1;// 未支付
    const STATUS_PAYING = 2;// 支付中
    const STATUS_PAID = 3;// 已支付
    const STATUS_CANCELED = 4;// 取消

    const REFUND_STATUS_PENDING = 0;
    const REFUND_STATUS_APPLIED = 6;
    const REFUND_STATUS_PROCESSING = 7;
    const REFUND_STATUS_SUCCESS = 8;
    const REFUND_STATUS_FAILED = 9;

    const SHIP_STATUS_PENDING = 1;
    const SHIP_STATUS_DELIVERED = 2;
    const SHIP_STATUS_RECEIVED = 3;

    public static $statusMap = [
        self::STATUS_UNPAY => '未支付',
        self::STATUS_PAYING => '支付中',
        self::STATUS_PAID => '已支付',
        self::STATUS_CANCELED => '取消',

    ];
    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];
    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING   => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED  => '已收货',
    ];

    const PAYMENT_METHOD_BANLANCE = 1;
    const PAYMENT_METHOD_ALIPAY = 2;
    const PAYMENT_METHOD_WECHAT = 3;

    public static $paymentMethodMap = [
        self::PAYMENT_METHOD_BANLANCE  => '余额支付',
        self::PAYMENT_METHOD_ALIPAY => '支付宝',
        self::PAYMENT_METHOD_WECHAT  => '微信',
    ];

    protected $fillable = [
        'no','address','total_amount','remark','paid_at','payment_method','payment_no','status','refund_no',
        'closed','reviewed','ship_status','ship_data','extra','refund_status'
    ];
    protected $dates = [
        'paid_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'address' => 'json',
        'ship_data' => 'json',
        //'extra'=>'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

}
