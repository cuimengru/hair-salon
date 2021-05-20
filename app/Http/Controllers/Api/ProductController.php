<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ProductLabel;
use App\Models\ProductSku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    //商城产品首页
    public function index(Request $request)
    {
        if($request->filter['type'] == 1){
            //集品类处的banner
            $product['banner'] = Advert::where('category_id','=',3)->orderBy('order', 'asc')->select('id','thumb', 'url')->get();
        }elseif ($request->filter['type'] == 2){
            //自营类处的banner
            $product['banner'] = Advert::where('category_id','=',5)->orderBy('order', 'asc')->select('id','thumb', 'url')->get();
        }elseif ($request->filter['type'] == 3){
            //闲置类处的banner
            $product['banner'] = Advert::where('category_id','=',4)->orderBy('order', 'asc')->select('id','thumb', 'url')->get();
        }

        //推荐产品
        $product['recommend_product'] = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('type') //商品类型 1集品 2自营 3闲置
            ])
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at', 'price') // 支持排序字段 更新时间 价格
            ->where('on_sale','=',1)
            ->where('is_recommend','=',1)
            ->select('id','title','country_name','label_id','image','price','original_price')
            ->get();

        foreach ($product['recommend_product'] as $k=>$value){
            $product['recommend_product'][$k]['label_name'] = ProductLabel::all()->whereIn('id',$value['label_id'])->pluck('name')->toArray();
        }

        //锦之选处的产品
        $product['choice_product'] = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('type') //商品类型 1集品 2自营 3闲置
            ])
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at', 'price') // 支持排序字段 更新时间 价格
            ->where('on_sale','=',1)
            ->select('id','title','country_name','label_id','image','price','original_price')
            ->paginate(8);
        foreach ($product['choice_product'] as $k=>$value){
            $product['choice_product'][$k]['label_name'] = ProductLabel::all()->whereIn('id',$value['label_id'])->pluck('name')->toArray();
        }

        return $product;
    }

    //产品搜索
    public function search(Request $request)
    {
        $product = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('type'), //商品类型 1集品 2自营 3闲置
                'title'
            ])
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at', 'price') // 支持排序字段 更新时间 价格
            ->where('on_sale','=',1)
            ->select('id','title','country_name','label_id','image','price','original_price')
            ->paginate(8);
        foreach ($product as $k=>$value){
            $product[$k]['label_name'] = ProductLabel::all()->whereIn('id',$value['label_id'])->pluck('name')->toArray();
        }

        return $product;
    }

    //产品详情
    public function show(Product $product,Request $request)
    {
        $product['label_name'] = ProductLabel::all()->whereIn('id',$product['label_id'])->pluck('name')->toArray();
        if($product['many_image']){
            foreach ($product['many_image'] as $k=>$value){
                $many_imageUrl[$k] = Storage::disk('public')->url($value);
            }
            $product['many_imageUrl'] = $many_imageUrl;
        }

        $product['product_sku'] = ProductSku::where('product_id','=',$product['id'])
            ->select('id','title','image','description','price','stock','product_id')
            ->get();

        $product['comments'] = Comment::where('product_id','=',$product['id'])
            ->where('type','=',2)
            ->where('status','=',1)
            ->select('user_id','product_id','product_sku_id','rate','render_content','render_image','render_video','created_at')
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
        foreach ($product['comments'] as $t=>$item){
            $user = User::where('id','=',$item['user_id'])->first();
            $product['comments'][$t]['user_name'] = $user->nickname;
            $product['comments'][$t]['user_image'] = $user->avatar_url;
        }
        return $product;
    }
}
