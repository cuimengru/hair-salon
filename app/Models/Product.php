<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'title', 'description', 'image', 'on_sale','rating', 'sold_count', 'review_count', 'price',
        'category_id','original_price','type','is_recommend','country','label_id','many_image','property',
        'package_mail','postage','country_name','selfcategory_id','idlecategory_id','order'
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
            return Storage::disk('oss')->url($this->image);
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

    //与集品商品类目关联
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function label()
    {
        return $this->hasMany(ProductLabel::class);
    }

    //与自营商品类目关联
    public function selfcategory()
    {
        return $this->belongsTo(SelfCategory::class);
    }

    //与闲置商品类目关联
    public function idlecategory()
    {
        return $this->belongsTo(IdleCategory::class);
    }


//hyh二级分类改造  //$data是接口接收到的数字
    public function scopeCategoryId($query, $c_id)
    {
        //查询该分类id在分类表中的情况 上方需要引入 use Illuminate\Support\Facades\DB;
        $cate_level = DB::table('categories')->where('id','=',$c_id)->first();

//        if($cate_level->level==1){ //不能用level字段来区分是几级分类，因为数据表中，这字段的记录很不准确！
//           如果是1级分类
            if($cate_level->parent_id==0){
//           如果是一级分类，获取其下属的二级分类
            $cate_level2 = DB::table('categories')->where('parent_id','=',$c_id)->select('id')->get();
//          print_R($cate_level2->toArray());
//          如果1级分类下有2级分类
            if($cate_level2->toArray()){
            foreach($cate_level2 as $key=>$obj){
                $c_data[]=json_encode($obj->id);
            }
            array_push($c_data, 9);
            return $query->whereIn('category_id', $c_data);
            }else{
//          如果1级分类下无2级分类
            return $query->where('category_id','=', $c_id);
            }
        }

//        如果是2级分类
        if($cate_level->parent_id>0){
            return $query->where('category_id','=', $c_id);
        }

    }


    public function scopeSelfcategoryId($query, $c_id)
    {
        $cate_level = DB::table('self_categories')->where('id','=',$c_id)->first();
        if($cate_level->parent_id==0){
            $cate_level2 = DB::table('self_categories')->where('parent_id','=',$c_id)->select('id')->get();
            if($cate_level2->toArray()){
                foreach($cate_level2 as $key=>$obj){
                    $c_data[]=json_encode($obj->id);
                }
                array_push($c_data, 9);
                return $query->whereIn('selfcategory_id', $c_data);
            }else{
                return $query->where('selfcategory_id','=', $c_id);
            }
        }
        if($cate_level->parent_id>0){
            return $query->where('selfcategory_id','=', $c_id);
        }
    }


}
