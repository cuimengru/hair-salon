<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use App\Models\BalanceRecord;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductLabel;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use App\Models\Fashion;
class IndexController extends Controller
{
    //猜你喜欢首页
    public function index(Request $request)
    {
        //广告banner
        $index = [];

        $advert = [];
        $advert['top'] = Advert::where('category_id','=',1)->orderBy('order', 'asc')->select('id','type','thumb','url','product_id')->get();

         //文教娱乐 hyh屏蔽改造
//        $advert['bottom'] = Advert::where('category_id','=',2)->orderBy('order', 'asc')->select('id','type','thumb','url','product_id')->get();
//        $index['ads'] = $advert;

//      hyhmodelname
        $advert_data['bottom'] = Advert::where('category_id','=',2)->orderBy('order', 'asc')->select('id','type','thumb','url','product_id')->get();
        $advert['bottom']['modelname'] = config('modelname.bottom');
        $advert['bottom']['list'] = $advert_data['bottom'];

        $index['ads'] = $advert;


        //推荐作品展示
        $production = Production::where('is_recommend','=',1)
            ->where('on_sale','=',1)
            ->orderBy('sort','desc')//hyh推荐作品排序
            ->orderBy('created_at','desc')
            ->select('id','title','thumb','video','description','content','type','rectangle_image','sort')
            ->limit(3)
            ->get();
//        $index['production'] = $production;
//        hyhmodelname
        $index['production']['modelname'] = config('modelname.production');
        $index['production']['list'] = $production;



        //锦之选 产品推荐
        $product = Product::where('is_recommend','=',1)->where('on_sale','=',1)
            ->orderBy('order','asc')
            ->select('id','title','country_name','label_id','image','price','original_price','order')
            ->get();
        foreach ($product as $k=>$value){
            $product[$k]['label_name'] = ProductLabel::all()->whereIn('id',$value['label_id'])->pluck('name')->toArray();
            if($value['original_price'] == 0.00){
                $product[$k]['original_price'] = null;
            }
            $product[$k]['product_sku_count'] = ProductSku::where('product_id','=',$value['id'])->count();
        }
//        $index['product'] = $product;
//        hyhmodelname
        $index['product']['modelname'] = config('modelname.product');
        $index['product']['list'] = $product;



//   资讯 hyh从作品首页搬过来的
        $index1['fashions'] = Fashion::where('is_recommend','=',1)
            ->orderBy('order','asc')
            ->orderBy('created_at','desc')
            ->select('id','title','thumb','description','created_at','updated_at')
            ->paginate(4);
        foreach ($index1['fashions'] as $k=>$value){
            $index1['fashions'][$k]['created_time'] = date("Y.m.d", strtotime($value['created_at']));
            $index1['fashions'][$k]['updated_time'] = date("Y.m.d", strtotime($value['updated_at']));
        }

//        hyhmodelname
        $index['fashions']['modelname'] = config('modelname.fashions');
        $index['fashions']['list'] = $index1['fashions'];




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
            //->whereIn('payment_method',[2,3])
            ->paginate(5);
        foreach ($balance as $k=>$value){
            if($value['payment_method'] == 2){
                $balance[$k]['payment_name'] = '支付宝';
            }elseif ($value['payment_method'] == 3){
                $balance[$k]['payment_name'] = '微信';
            }elseif ($value['payment_method'] == 1){
                $balance[$k]['payment_name'] = '后台充值';
            }
            $balance[$k]['status'] = '充值';
        }
        return $balance;
    }

}
