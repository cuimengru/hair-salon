<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReserveOrder extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'reserve_id','user_id','service_project','time','num','phone','remark','designer_id'
    ];

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
}
