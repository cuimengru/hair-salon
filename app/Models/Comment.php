<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Comment extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'type', 'user_id','order_id', 'reserveorder_id', 'designer_id', 'product_id','product_sku_id','rate',
        'render_content','render_image','render_video','status'
    ];
    protected $appends = [
        'video_url'
    ];
    protected $casts = [
        'render_image'=>'array',
    ];
    public function setRenderImageAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['render_image'] = json_encode($value);
        }
    }

    public function getRenderImageAttribute($value)
    {
        return json_decode($value, true);
    }

    // 返回图片链接
   /* public function getThumbUrlAttribute()
    {
        if ($this->render_image) {
            return Storage::disk('public')->url($this->render_image);
        } else {
            return '';
        }
    }*/

    public function getVideoUrlAttribute()
    {
        if ($this->render_image) {
            return Storage::disk('public')->url($this->render_video);
        } else {
            return '';
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function Order()
    {
        return $this->belongsTo(Order::class);
    }
    public function reserveorder()
    {
        return $this->belongsTo(ReserveOrder::class);
    }
    public function designer()
    {
        return $this->belongsTo(Designer::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function productsku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
