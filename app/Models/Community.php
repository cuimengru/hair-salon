<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Community extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'user_id','title','content','many_images','video','status','video_url'
    ];
    protected $appends = [
        'video_play_url'
    ];
    /*protected $casts = [
        'many_images'=>'array',
    ];*/

    public function getVideoPlayUrlAttribute()
    {
        if ($this->video) {
            return Storage::disk('public')->url($this->video);
        } else {
            return '';
        }
    }

    //多图
   /* public function setManyImagesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['many_images'] = json_encode($value);
        }
    }
    public function getManyImagesAttribute($value)
    {
        return json_decode($value, true);
    }*/

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
