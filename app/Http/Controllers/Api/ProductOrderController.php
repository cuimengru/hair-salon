<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductOrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductOrderController extends Controller
{
    //创建商品订单
    public function store(ProductOrderRequest $request)
    {
        $user = $request->user();

        //开启一个数据库事务
        $order = DB::transaction(function () use ($user, $request) {
            $address = UserAddress::findOrFail($request->address_id);
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order = new Order([
                'address' => [ // 将地址信息放入订单中
                    'address' => $address->full_address,
                    //'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $request->remark,
                'total_amount' => 0,
                'status' => 1,
                'ship_status' => 1,
            ]);

            //订单关联到当前用户
            $order->user()->associate($user);

            //写入数据库
            $order->save();

            $totalAmount = 0;
            $items = $request->items;
            //遍历用户提交的 SKU
            foreach ($items as $data){
                $sku = ProductSku::findOrFail($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price' => $sku->price,
                ]);

                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->user_id = $user->id;
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if($sku->decreaseStock($data['amount']) <= 0){
                    $data['message'] = "该商品库存不足";
                    return response()->json($data, 403);
                }
            }

            //更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            //将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });

        $this->dispatch(new CloseOrder($order,config('order.order_ttl')));
        return $order;
    }

    //全部订单
    public function index(Request $request)
    {
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return $orders;
    }
}
