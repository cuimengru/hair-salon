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
        'category_id','original_price','type','is_recommend','country'
    ];
    protected $casts = [
        'on_sale' => 'boolean', // on_sale 是一个布尔类型的字段
    ];
    protected $appends = [
        'image_url',
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
