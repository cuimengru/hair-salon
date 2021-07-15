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

    const REFUND_STATUS_PENDING = 5;
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
        self::REFUND_STATUS_PROCESSING => '已申请退款', //目前用的这个
        //self::REFUND_STATUS_PROCESSING => '退款中',
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
        'closed','reviewed','ship_status','ship_data','extra','refund_status','balance','refund_number'
    ];
    protected $dates = [
        'paid_at',
    ];
    protected $appends = [
        'remaining_balance',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address' => 'json',
        'ship_data' => 'json',
        'extra'=>'json',
    ];

    // 返回原余额
    public function getRemainingBalanceAttribute()
    {
        if ($this->balance) {
            return number_format(($this->balance) + ($this->total_amount),2);
        } else {
            return '';
        }
    }

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 no 字段为空
            if (!$model->no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->no) {
                    return false;
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAddressAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = json_encode(array_values($value));
    }
    /*public function getShipDataAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setShipDataAttribute($value)
    {
        $this->attributes['ship_data'] = json_encode(array_values($value));
    }*/
    /*public function getExtraAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setExtraAttribute($value)
    {
        $this->attributes['extra'] = json_encode(array_values($value));
    }*/


    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('no', $no)->exists()) {
                return $no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }
}
