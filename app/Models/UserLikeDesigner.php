<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLikeDesigner extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'type','user_id','product_id','selfproduct_id','idleproduct_id','designer_id','production_id','count'
    ];

    //关联用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //关联商品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //关联设计师
    public function designer()
    {
        return $this->belongsTo(Designer::class);
    }

    //关联作品
    public function production()
    {
        return $this->belongsTo(Production::class);
    }
}
