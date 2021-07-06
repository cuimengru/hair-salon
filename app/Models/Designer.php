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
        'name','description','thumb', 'position', 'rating','many_images','certificate','honor','score','label_id',
        'is_recommend','employee_number','is_employee'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'many_images' => 'array',
        'certificate' => 'json',
        'honor' => 'json',
    ];

    protected $appends = [
        'thumb_url','label_name'
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

    //标签
    public function getLabelNameAttribute()
    {
        $data = DesignerLabel::all('id', 'name')
            ->whereIn('id', explode(',', $this->attributes['label_id']))
            ->pluck('name')
            ->toArray();
        return array_values($data);
    }

    public function getLabelIdAttribute($value)
    {
        return explode(',', $value);
    }

    public function setLabelIdAttribute($value)
    {
        $this->attributes['label_id'] = implode(',', $value);
    }

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

    //证书
    public function setCertificateAttribute($value)
    {
        $this->attributes['certificate'] = json_encode(array_values($value));
    }

    public function getCertificateAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    //荣誉
    public function setHonorAttribute($value)
    {
        $this->attributes['honor'] = json_encode(array_values($value));
    }

    public function getHonorAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function label()
    {
        return $this->hasMany(DesignerLabel::class);
    }
}
