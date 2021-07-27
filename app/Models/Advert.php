<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Advert extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'title', 'description','thumb', 'video', 'video_url', 'is_recommend','category_id','content','type','product_id'
    ];
    protected $appends = [
        'thumb_url'
    ];
    // 返回图片链接
    public function getThumbUrlAttribute()
    {
        if ($this->thumb) {
            return Storage::disk('oss')->url($this->thumb);
        } else {
            return '';
        }
    }

    public function category()
    {
        return $this->belongsTo(AdvertCategory::class);
    }
}
