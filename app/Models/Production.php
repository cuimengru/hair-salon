<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Production extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'title','description','thumb', 'video', 'rating','designer_id','content','is_recommend','type','many_images'
    ];

    protected $casts = [
        'many_images'=>'array',
    ];

    protected $appends = [
        'thumb_url','video_url'
    ];
    // 返回图片链接
    public function getThumbUrlAttribute()
    {
        if ($this->thumb) {
            return Storage::disk('public')->url($this->thumb);
        } else {
            return '';
        }
    }
    // 返回 Viemo 视频 Mp4 地址
    public function getVideoUrlAttribute()
    {
        if ($this->video) {
            return Storage::disk('public')->url($this->video);
        } else {
            return "";
        }
    }

    public function setManyImagesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['many_images'] = json_encode($value);
        }
    }

    public function getManyImagesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function designer()
    {
        return $this->belongsTo(Designer::class);
    }
}
