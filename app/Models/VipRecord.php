<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VipRecord extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'user_id', 'paid_at','payment_method', 'payment_no', 'total_amount','admin_id','original_balance','no','remark'
    ];

    protected $dates = [
        'paid_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
