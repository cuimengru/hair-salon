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

    const PAYMENT_METHOD_BANLANCE = 1;
    const PAYMENT_METHOD_ALIPAY = 2;
    const PAYMENT_METHOD_WECHAT = 3;

    public static $paymentMethodMap = [
        self::PAYMENT_METHOD_BANLANCE  => '余额支付',
        self::PAYMENT_METHOD_ALIPAY => '支付宝',
        self::PAYMENT_METHOD_WECHAT  => '微信',
    ];

    protected $fillable = [
        'reserve_id','user_id','service_project','time','num','phone','remark','designer_id','money','no',
        'payment_method', 'status','reviewed','paid_at','payment_no','date'
    ];

    protected $casts = [
        'reviewed'  => 'boolean',
    ];

    protected $appends = [
        'reserve_date',
    ];

    //预约时间
    public function getReserveDateAttribute(){
        if($this->date && $this->time){
            return ($this->date).' '.($this->time);
        }else{
            return '';
        }
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
        return $this->belongsTo(ServiceProject::class);
    }
}
