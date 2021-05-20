<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'title', 'description', 'image', 'on_sale','rating', 'sold_count', 'review_count', 'price',
        'category_id','original_price','type','is_recommend','country','label_id','many_image','property',
        'package_mail','postage','country_name'
    ];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
        'property' => 'json',
        'many_image'=>'array',
    ];
    protected $appends = [
        'image_url','label_name'
    ];
    // 返回图片链接
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return Storage::disk('public')->url($this->image);
        } else {
            return '';
        }
    }

    public function setManyImageAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['many_image'] = json_encode($value);
        }
    }

    public function getManyImageAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getPropertyAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setPropertyAttribute($value)
    {
        $this->attributes['property'] = json_encode(array_values($value));
    }

    public function getLabelNameAttribute()
    {
        $data = ProductLabel::all('id', 'name')
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

    // 与商品SKU关联
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function label()
    {
        return $this->hasMany(ProductLabel::class);
    }
}
