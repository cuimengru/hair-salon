<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReserveOrder extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    const STATUS_UNPAY = 1;// 未支付
    const STATUS_PAYING = 2;// 支付中
    const STATUS_PAID = 3;// 已支付
    const STATUS_CANCELED = 4;// 取消
    //const STATUS_REFUND = 5;// 退款成功
    //const STATUS_UNREFUND = 6;// 退款成功

    public static $statusMap = [
        self::STATUS_UNPAY => '未支付',
        self::STATUS_PAYING => '支付中',
        self::STATUS_PAID => '已支付',
        self::STATUS_CANCELED => '取消',
        //self::STATUS_REFUND => '退款成功',
        //self::STATUS_UNREFUND => '退款失败',
    ];

    const REFUND_STATUS_PENDING = 5;
    const REFUND_STATUS_APPLIED = 6;
    const REFUND_STATUS_PROCESSING = 7;
    const REFUND_STATUS_SUCCESS = 8;
    const REFUND_STATUS_FAILED = 9;

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING    => '未退款',
        self::REFUND_STATUS_APPLIED    => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS    => '退款成功',
        self::REFUND_STATUS_FAILED     => '退款失败',
    ];

    const PAYMENT_METHOD_BANLANCE = 1;
    const PAYMENT_METHOD_ALIPAY = 2;
    const PAYMENT_METHOD_WECHAT = 3;
    const PAYMENT_METHOD_XIANJIN = 4;
    const PAYMENT_METHOD_MINI = 5;

    public static $paymentMethodMap = [
        self::PAYMENT_METHOD_BANLANCE  => '余额支付',
        self::PAYMENT_METHOD_ALIPAY => '支付宝',
        self::PAYMENT_METHOD_WECHAT  => '微信',
        self::PAYMENT_METHOD_XIANJIN  => '现金',
        self::PAYMENT_METHOD_MINI  => '小程序',
    ];

    protected $fillable = [
        'reserve_id','user_id','service_project','time','num','phone','remark','designer_id','money','no',
        'payment_method', 'status','reviewed','paid_at','payment_no','date','type','refund_status','closed','balance',
        'ship_status','refund_number'
    ];

    protected $casts = [
        'reviewed'  => 'boolean',
        'closed'    => 'boolean',
    ];

    protected $appends = [
        'reserve_date','service_name','remaining_balance'
    ];

    //预约时间
    public function getReserveDateAttribute(){
        if($this->date && $this->time){
            return ($this->date).' '.($this->time);
        }else{
            return '';
        }
    }

    //预约项目
    public function getServiceNameAttribute(){
        if($this->service_project){
           $service_project = ServiceProject::find($this->service_project);
           if($service_project){ //hyh做非空判断
           return ($service_project->name).'&nbsp;&nbsp;&nbsp;&nbsp;  ¥'.($service_project->price);
           }else{
               return '';
           }
        }else{
            return '';
        }
    }

    // 返回剩下余额
    public function getRemainingBalanceAttribute()
    {
        if ($this->balance) {
            return number_format(($this->balance) + ($this->money),2);
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

    public function reserve()
    {
        return $this->belongsTo(ReserveInformation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designer()
    {
        return $this->belongsTo(Designer::class);
    }
    public function service()
    {
        return $this->belongsTo(ServiceProject::class,'service_project');
    }

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
