<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityLike extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'user_id','community_id'
    ];

    // 关联 用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //关联社区
    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
