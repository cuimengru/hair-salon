<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //添加商品到购物车
    public function store(AddCartRequest $request)
    {
        $user = $request->user();
        $skuId  = $request->sku_id;
        $amount = $request->amount;

        // 从数据库中查询该商品是否已经在购物车中
        if($cart = $user->cartItems()->where('product_sku_id',$skuId)->first()){
            //如果存在则直接叠加商品数量
            $cart->update([
                'amount' => $cart->amount + $amount,
            ]);
        }else{
            // 否则创建一个新的购物车记录
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        $data['message'] = "Product Added OK!";
        return response()->json($data, 200);
    }

    //在购物车中删除商品
    public function destroy(Request $request)
    {
        $user = $request->user();
        foreach ($request->cart_id as $k=>$value){
            $cart[$k] = CartItem::findOrFail($value);

            if($user->id != $cart[$k]->user_id){
                $data['message'] = "This action is unauthorized."; // 验证权限
                return response()->json($data, 500);
            }

            $cart[$k]->delete();
        }

        $data['message'] = "Cart Deleted OK!";
        return response()->json($data, 200);
    }

    //购物车列表
    public function index(Request $request)
    {
        $user = $request->user();
        $cart = CartItem::where('user_id','=',1)
            ->orderBy('created_at','desc')
            ->get();
        foreach ($cart as $k=>$value){
            $cart[$k]['product_sku'] = ProductSku::where('id','=',$value['product_sku_id'])
                ->select('id','title','image','description','price','product_id')
                ->first();
            $product = Product::findOrFail($cart[$k]['product_sku']['product_id']);
            $cart[$k]['product_sku']['country_name'] = $product->country_name;
        }
        return $cart;
    }


}
