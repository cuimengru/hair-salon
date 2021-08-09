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
        'title','description','thumb', 'video', 'rating','designer_id','content','is_recommend','type','many_images',
        'rectangle_image','order','gender','age_id','length_id','color_id','style_id','on_sale','height_id','face_id',
        'project_id','hair_id','sort'
    ];

    protected $casts = [
        'many_images'=>'array',
        'style_id' => 'array',
        'age_id' => 'array',
        'hair_id' => 'array',
        'height_id' => 'array', //hyh身高改多选
        'color_id' => 'array',
        'length_id' => 'array',
        'face_id' => 'array',
        'project_id' => 'array'

    ];

    protected $appends = [
        'thumb_url','video_url','image_url'
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
    // 返回 Viemo 视频 Mp4 地址
    public function getVideoUrlAttribute()
    {
        if ($this->video) {
            return Storage::disk('oss')->url($this->video);
        } else {
            return "";
        }
    }

    public function getImageUrlAttribute()
    {
        if ($this->rectangle_image) {
            return Storage::disk('oss')->url($this->rectangle_image);
        } else {
            return '';
        }
    }

//hyh作品标题改为非必填 不设置这个的话 接口返回null 不是想要的
    public function getTitleAttribute()
    {
        if (empty($this->title)) {
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

    public function setStyleIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['style_id'] = json_encode($value);
        }
    }

    public function getStyleIdAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setAgeIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['age_id'] = json_encode($value);
        }
    }

    public function getAgeIdAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setHairIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['hair_id'] = json_encode($value);
        }
    }

    public function getHairIdAttribute($value)
    {
        return json_decode($value, true);
    }

//hyh身高改多选
    public function setHeightIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['height_id'] = json_encode($value);
        }
    }
    public function getHeightIdAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setColorIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['color_id'] = json_encode($value);
        }
    }
    public function getColorIdAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setLengthIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['length_id'] = json_encode($value);
        }
    }
    public function getLengthIdAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setFaceIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['face_id'] = json_encode($value);
        }
    }
    public function getFaceIdAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setProjectIdAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['project_id'] = json_encode($value);
        }
    }
    public function getProjectIdAttribute($value)
    {
        return json_decode($value, true);
    }


    public function designer()
    {
        return $this->belongsTo(Designer::class);
    }

    public function age()
    {
        return $this->belongsTo(ProductionAge::class);
    }

    public function color()
    {
        return $this->belongsTo(ProductionColor::class);
    }

    public function length()
    {
        return $this->belongsTo(ProductionLength::class);
    }

    public function style()
    {
        return $this->hasMany(ProductionStyle::class);
    }

    public function face()
    {
        return $this->belongsTo(ProductionFace::class);
    }

    public function height()
    {
        return $this->belongsTo(ProductionHeight::class);
    }

    public function project()
    {
        return $this->belongsTo(ProductionProject::class);
    }

    public function hair()
    {
        return $this->belongsTo(ProductionHair::class);
    }
}
