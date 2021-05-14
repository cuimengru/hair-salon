<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Designer extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    protected $fillable = [
        'name','description','thumb', 'position', 'rating'
    ];
    protected $appends = [
        'thumb_url'
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
}
