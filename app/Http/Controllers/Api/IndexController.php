<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductLabel;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    //猜你喜欢首页
    public function index(Request $request)
    {
        //广告banner
        $index = [];

        $advert = [];
        $advert['top'] = Advert::where('category_id','=',1)->orderBy('order', 'asc')->select('id','thumb', 'url')->get();
        //文教娱乐
        $advert['bottom'] = Advert::where('category_id','=',2)->orderBy('order', 'asc')->select('id','thumb', 'url')->get();
        $index['ads'] = $advert;

        //推荐作品展示
        $production = Production::where('is_recommend','=',1)->select('id','title','thumb','video','description','content')->limit(3)->get();
        $index['production'] = $production;

        //锦之选 产品推荐
        $product = Product::where('is_recommend','=',1)->where('on_sale','=',1)
            ->select('id','title','country_name','label_id','image','price','original_price')
            ->get();
        foreach ($product as $k=>$value){
            $product[$k]['label_name'] = ProductLabel::all()->whereIn('id',$value['label_id'])->pluck('name')->toArray();
        }
        $index['product'] = $product;
        return $index;
    }
}
