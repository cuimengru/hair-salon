<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Exception\InternalErrorException;

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

    //减库存
    public function decreaseStock($amount)
    {
        if($amount < 0){
            throw new InternalErrorException('减库存不可小于0');
        }

        return $this->where('id',$this->id)->where('stock','>=',$amount)->decrement('stock',$amount);
    }

    public function addStock($amount)
    {
        if($amount < 0){
            throw new InternalErrorException('加库存不可小于0');
        }

        $this->increment('stock',$amount);
    }
}
