<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductSku extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'price', 'stock','product_id','image'];

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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
