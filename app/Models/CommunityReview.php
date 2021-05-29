<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityReview extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'user_id','replyuser_id','community_id','message'
    ];

    protected $casts = [
        'message'=>'json',
    ];

    public function getMessageAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = json_encode(array_values($value));
    }

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
