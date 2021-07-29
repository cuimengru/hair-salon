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
        if (!$sku = ProductSku::find($skuId)) {
            $data['message'] = "该商品不存在";
            return response()->json($data, 403);
            //return $fail('该商品不存在');
        }
        if (!$sku->product->on_sale) {
            $data['message'] = "该商品未上架";
            return response()->json($data, 403);
                //return $fail('该商品未上架');
        }
        if ($sku->stock === 0) {
            $data['message'] = "该商品已售完";
            return response()->json($data, 403);
            //return $fail('该商品已售完');
        }
        if ($amount > 0 && $sku->stock < $amount) {
            $data['message'] = "该商品库存不足";
            return response()->json($data, 403);
            //return $fail('该商品库存不足');
        }


        // 从数据库中查询该商品是否已经在购物车中
        if($cart = $user->cartItems()->where('product_sku_id',$skuId)->first()){
            $productSku = ProductSku::where('id','=',$skuId)->first();
            if($productSku->stock == 1 ){
                if($cart){
                    $data['message'] = "库存为1，已经添加，不能再添加了!";
                    return response()->json($data, 403);
                }
            }else{
                //如果存在则直接叠加商品数量
                $cart->update([
                    'amount' => $cart->amount + $amount,
                ]);
            }

        }else{
            // 否则创建一个新的购物车记录
            $cart = new CartItem(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }

        $data['message'] = "加入购物车成功!";
        $data['count']= CartItem::where('user_id','=',$user->id)->count();

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

        $data['message'] = "删除成功!";
        return response()->json($data, 200);
    }

    //购物车列表
    public function index(Request $request)
    {
        $user = $request->user();
        $cart = CartItem::where('user_id','=',$user->id)
            ->orderBy('created_at','desc')
            ->get();
        foreach ($cart as $k=>$value){
            $cart[$k]['product_sku'] = ProductSku::where('id','=',$value['product_sku_id'])
                ->select('id','title','image','description','price','product_id','stock')
                ->first();
            $product = Product::findOrFail($cart[$k]['product_sku']['product_id']);
            $cart[$k]['product_sku']['country_name'] = $product->country_name;
            $cart[$k]['product_sku']['product_title'] = $product->title;
        }
        return $cart;
    }

    //减去购物车商品数量
    public function update($cartId,Request $request)
    {
        $cart = CartItem::findOrFail($cartId);
        $amount = $request->amount;
        /*if($cart->amount == 1){
            $data['message'] = "该宝贝不能减少了！";
            return response()->json($data, 403);
        }*/
        $user = $request->user();
        if($cart = $user->cartItems()->where('product_sku_id',$cart->product_sku_id)->first()){
        //如果存在则直接叠加商品数量
        $cart->update([
            'amount' => $amount,
            ]);
        }
        $data['message'] = "减去成功!";
        return response()->json($data, 200);
    }
}
