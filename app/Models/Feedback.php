<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    protected $fillable = [
        'user_id','content','many_images'
    ];
    protected $casts = [
        'many_images' => 'array',
    ];

    //多图
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
