<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\CartItem;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ProductLabel;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserLikeDesigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function MongoDB\BSON\toJSON;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    //商城产品首页
    public function index(Request $request)
    {
//      hyhmodelname
        $product['category_aim'][0] = Array('type'=>'1','name'=>config('modelname.cate1'));//1 集品
        $product['category_aim'][1] = Array('type'=>'2','name'=>config('modelname.cate2'));//2 自营
        $product['category_aim'][2] = Array('type'=>'3','name'=>config('modelname.cate3'));//3 闲置=转让

        if($request->filter['type'] == 1) {

            $product['category_aim'];//hyhmodelname

            //集品类处的banner
            $product['banner'] = Advert::where('category_id','=',3)->orderBy('order', 'asc')->select('id','type','thumb','url','product_id')->get();
//      hyh如果广告链接的产品对此做是否存在和是否上架的判断
            foreach ($product['banner'] as $k=>$value){
                $product_sale=Product::where('id','=',$value['product_id'])->first();
                if($product_sale && $product_sale['on_sale']==1){
                    $product['banner'][$k]['product_state']="1";
                }else{
                    $product['banner'][$k]['product_state']="0";//不存在或已下架
                }
            }

        }elseif ($request->filter['type'] == 2){
            $product['category_aim'];
            //自营类处的banner
            $product['banner'] = Advert::where('category_id','=',5)->orderBy('order', 'asc')->select('id','type','thumb','url','product_id')->get();
//      hyh如果广告链接的产品对此做是否存在和是否上架的判断
            foreach ($product['banner'] as $k=>$value){
                $product_sale=Product::where('id','=',$value['product_id'])->first();
                if($product_sale && $product_sale['on_sale']==1){
                    $product['banner'][$k]['product_state']="1";
                }else{
                    $product['banner'][$k]['product_state']="0";//不存在或已下架
                }
            }
        }elseif ($request->filter['type'] == 3){
            $product['category_aim'];
            //闲置类处的banner
            $product['banner'] = Advert::where('category_id','=',4)->orderBy('order', 'asc')->select('id','type','thumb','url','product_id')->get();
//      hyh如果广告链接的产品对此做是否存在和是否上架的判断
            foreach ($product['banner'] as $k=>$value){
                $product_sale=Product::where('id','=',$value['product_id'])->first();
                if($product_sale && $product_sale['on_sale']==1){
                    $product['banner'][$k]['product_state']="1";
                }else{
                    $product['banner'][$k]['product_state']="0";//不存在或已下架
                }
            }
        }

        //推荐产品
        if($request->filter['type'] == 1 || $request->filter['type'] == 2){
            //集品类
            $product1['recommend_product'] = QueryBuilder::for(Product::class)
                ->allowedFilters([
                    AllowedFilter::exact('type') //商品类型 1集品 2自营 3闲置
                ])
                ->defaultSort('-created_at') //按照创建时间排序
                ->allowedSorts('updated_at', 'price') // 支持排序字段 更新时间 价格
                ->where('on_sale','=',1)
                ->where('is_recommend','=',1)
                ->select('id','title','country_name','label_id','image','price','original_price')
                ->get();
            foreach ($product1['recommend_product'] as $k=>$value){
                $product1['recommend_product'][$k]['label_name'] = ProductLabel::all()->whereIn('id',$value['label_id'])->pluck('name')->toArray();
                if($value['original_price'] == 0.00){
                    $product1['recommend_product'][$k]['original_price'] = null;
                }
                $product1['recommend_product'][$k]['product_sku_count'] = ProductSku::where('product_id','=',$value['id'])->count();
            }
        }
//        hyhmodelname
        if($request->filter['type'] == 1){//集品
        $product['recommend_product']['modelname'] = config('modelname.cate1_recommend');
        $product['recommend_product']['list'] = $product1;
        }
        if($request->filter['type'] == 2){//自营
            $product['recommend_product']['modelname'] = config('modelname.cate2_recommend');
            $product['recommend_product']['list'] = $product1;
        }




        //锦之选处的产品
        $product2['choice_product'] = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('type') //商品类型 1集品 2自营 3闲置
            ])
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at', 'price') // 支持排序字段 更新时间 价格
            ->where('on_sale','=',1)
            ->select('id','title','country_name','label_id','image','price','original_price')
            ->paginate(8);
        foreach ($product2['choice_product'] as $k=>$value){
            $product2['choice_product'][$k]['label_name'] = ProductLabel::all()->whereIn('id',$value['label_id'])->pluck('name')->toArray();
            if($value['original_price'] == 0.00){
                $product2['choice_product'][$k]['original_price'] = null;
            }
            $product2['choice_product'][$k]['product_sku_count'] = ProductSku::where('product_id','=',$value['id'])->count();
        }


//        hyhmodelname
        if($request->filter['type'] == 1){//集品
            $product['choice_product']['modelname'] = config('modelname.cate1_list');;
            $product['choice_product']['list'] = $product2;
        }
        if($request->filter['type'] == 2){//自营
            $product['choice_product']['modelname'] = config('modelname.cate2_list');;
            $product['choice_product']['list'] = $product2;
        }
        if($request->filter['type'] == 3){//闲置
            $product['choice_product']['modelname'] = config('modelname.cate3_list');;
            $product['choice_product']['list'] = $product2;
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
            if($value['original_price'] == 0.00){
                $product[$k]['original_price'] = null;
            }
            $product[$k]['product_sku_count'] = ProductSku::where('product_id','=',$value['id'])->count();
        }

        return $product;
    }

    //产品详情
    public function show(Product $product,Request $request)
    {
        $product['label_name'] = ProductLabel::all()->whereIn('id',$product['label_id'])->pluck('name')->toArray();
        if($product['original_price'] == 0.00){
            $product['original_price'] = null;
        }
        if($product['many_image']){
            foreach ($product['many_image'] as $k=>$value){
                $many_imageUrl[$k] = Storage::disk('oss')->url($value);
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
            $product['comments'][$t]['user_avatar'] = $user->avatar_url;
            if ($item['render_image']) {
                foreach ($item['render_image'] as $i => $image) {
                    $render_imageUrl[$i] = Storage::disk('oss')->url($image);
                }
                $product['comments'][$t]['render_imageUrl'] = $render_imageUrl;
            }
        }

        //用户是否收藏
        if($request->user_id){
            $user_product = DB::table('user_favorite_products')
                ->where('user_id','=',$request->user_id)
                ->where('product_id','=',$product->id)
                ->first();
            if($user_product){
                $product['favor_product'] = 1; //已收藏
            }else{
                $product['favor_product'] = 0; //未收藏
            }
            $record = UserLikeDesigner::whereUserId($request->user_id)->where('product_id','=',$product['id'])->first();
                if($record){
                    $record->update([
                        'count' => $record->count + 1,
                    ]);
                }else{
                    if($product['type'] == 1){
                        UserLikeDesigner::create([
                            'user_id' => $request->user_id,
                            'product_id' => $product['id'],
                            'type' => 1,
                        ]);
                    }
                }

            $record3 = UserLikeDesigner::whereUserId($request->user_id)->where('idleproduct_id','=',$product['id'])->first();
                if($record3){
                    $record3->update([
                        'count' => $record3->count + 1,
                    ]);
                }else{
                    if($product['type'] == 3){
                        UserLikeDesigner::create([
                            'user_id' => $request->user_id,
                            'idleproduct_id' => $product['id'],
                            'type' => 2,
                        ]);
                    }
                }
            $product['cart_count']= CartItem::where('user_id','=',$request->user_id)->count();

        }else{
            $product['favor_product'] = 0; //未收藏
            $product['cart_count'] = 0;
        }

        return $product;
    }

    //根据分类获取商品
    public function allproducts(Request $request)
    {
        $product = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('type'), //商品类型 1集品 2自营 3闲置
//              AllowedFilter::exact('category_id'), //商品分类 集品////hyh二级分类改造
                AllowedFilter::scope('category_id'),//hyh二级分类改造
//              AllowedFilter::exact('selfcategory_id'), //商品分类 自营
                AllowedFilter::scope('selfcategory_id'),
                'title'
            ])
            ->where('on_sale','=',1)//hyh补充
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at', 'price') // 支持排序字段 更新时间 价格
            ->select('id','title','country_name','label_id','image','price','original_price')
            ->paginate(8);
        foreach ($product as $k=>$value){
            $product[$k]['label_name'] = ProductLabel::all()->whereIn('id',$value['label_id'])->pluck('name')->toArray();
            if($value['original_price'] == 0.00){
                $product[$k]['original_price'] = null;
            }
            $product[$k]['product_sku_count'] = ProductSku::where('product_id','=',$value['id'])->count();
        }

        return $product;
    }

    //收藏商品
    public function favor(Product $product,Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            $data['message'] = " 已经收藏！";
            return response()->json($data, 403);
        }
        $user->favoriteProducts()->attach($product);

        $data['message'] = "收藏成功！";
        return response()->json($data, 200);
    }

    //取消收藏商品
    public function disfavor(Product $product,Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        $data['message'] = "取消成功！";
        return response()->json($data, 200);
    }

    //收藏商品列表
    public function followlist(Request $request)
    {
        $product = $request->user()->favoriteProducts()->paginate(8);
        foreach ($product as $k=>$value){
            unset($product[$k]['category_id']);
            unset($product[$k]['selfcategory_id']);
            unset($product[$k]['idlecategory_id']);
            unset($product[$k]['country']);
            unset($product[$k]['description']);
            unset($product[$k]['many_image']);
            unset($product[$k]['on_sale']);
            unset($product[$k]['sold_count']);
            unset($product[$k]['review_count']);
            unset($product[$k]['property']);
            unset($product[$k]['package_mail']);
            unset($product[$k]['postage']);
            unset($product[$k]['type']);
            unset($product[$k]['is_recommend']);
            unset($product[$k]['created_at']);
            unset($product[$k]['updated_at']);
            unset($product[$k]['pivot']);
        }
        return $product;
    }

}
