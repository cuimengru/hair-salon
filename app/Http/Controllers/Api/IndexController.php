<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use App\Models\BalanceRecord;
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
        $production = Production::where('is_recommend','=',1)->select('id','title','thumb','video','description','content','type','rectangle_image')->limit(3)->get();
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

    //关于锦之都
    public function jinzhido()
    {
        $jinzhido['description'] = config('website.description');
        $jinzhido['content'] = config('website.content');
        $jinzhido['email'] = config('website.email');
        $jinzhido['phone'] = config('website.phone');
        return $jinzhido;
    }

    //充值记录列表
    public function balancelist(Request $request)
    {
        $user = $request->user();
        $balance = BalanceRecord::where('user_id','=',$user->id)
            ->orderBy('paid_at','desc')
            ->whereIn('payment_method',[2,3])
            ->paginate(5);
        foreach ($balance as $k=>$value){
            if($value['payment_method'] == 2){
                $balance[$k]['payment_name'] = '支付宝';
            }elseif ($value['payment_method'] == 3){
                $balance[$k]['payment_name'] = '微信';
            }
            $balance[$k]['status'] = '充值';
        }
        return $balance;
    }
}
