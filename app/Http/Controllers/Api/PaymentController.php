<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReserveOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //商品订单支付
    public function productStore($orderId,Request $request)
    {
        //判断订单是否属于当前用户
        $user = $request->user();
        $order = Order::where('user_id','=',$user->id)
            ->where('id','=',$orderId)->first();
        if(!$order){
            $data['message'] = "Without permission!";
            return response()->json($data, 403);
        }

        //订单已支付或者已关闭
        if($order->paid_at || $order->closed){
            $data['message'] = "订单状态不正确!";
            return response()->json($data, 403);
        }

        $payment_method = $request->payment_method; //支付方式
        foreach ($payment_method as $k=>$value){

        }
        //余额支付
        if($payment_method == Order::PAYMENT_METHOD_BANLANCE){
            if($user){
                //查询用户余额是否足够支付本订单
                if($user->balance > 0 && $user->balance >= $order->total_amount){
                    $payment_id = Order::PAYMENT_METHOD_BANLANCE;
                    $balance = $order->total_amount;// 使用余额支付本订单
                    $user->balance = $user->balance - $balance;
                    $user->update();
                    // 更新订单付款信息
                    $order->update([
                        'balance'=>$balance,
                        'total_amount' => $balance,
                        'status' => Order::STATUS_PAID,// 更新订单状态
                        'payment_method' => $payment_id,
                        'paid_at'=>Carbon::now('Asia/shanghai'),
                    ]);
                }else{
                    //余额不足，使用支付宝支付剩下的
                        if($payment_method == Order::PAYMENT_METHOD_ALIPAY){
                            return '111';
                        }elseif ($payment_method == Order::PAYMENT_METHOD_WECHAT){
                            return '222';
                        }


                }
            }
        }
        return $order;
    }

    //预约订单支付
    public function reserveStore($orderId,Request $request)
    {
        //判断订单是否属于当前用户
        $user = $request->user();
        $order = ReserveOrder::where('user_id','=',$user->id)
            ->where('id','=',$orderId)->first();
        if(!$order){
            $data['message'] = "Without permission!";
            return response()->json($data, 403);
        }

        //订单已支付或者已关闭
        if($order->paid_at || $order->closed){
            $data['message'] = "订单状态不正确!";
            return response()->json($data, 403);
        }

        $payment_method = $request->payment_method; //支付方式

        //余额支付
        if($payment_method == ReserveOrder::PAYMENT_METHOD_BANLANCE){
            if($user){
                //查询用户余额是否足够支付本订单
                if($user->balance > 0 && $user->balance >= $order->total_amount){
                    $payment_id = ReserveOrder::PAYMENT_METHOD_BANLANCE;
                    $balance = $order->money;// 使用余额支付本订单
                    $user->balance = $user->balance - $balance;
                    $user->update();
                    // 更新订单付款信息
                    $order->update([
                        'balance'=>$balance,
                        'total_amount' => $balance,
                        'status' => ReserveOrder::STATUS_PAID,// 更新订单状态
                        'payment_method' => $payment_id,
                        'paid_at'=>Carbon::now('Asia/shanghai'),
                    ]);
                }else{
                    //余额不足，使用支付宝支付剩下的
                    if($payment_method == Order::PAYMENT_METHOD_ALIPAY){
                        return '111';
                    }elseif ($payment_method == Order::PAYMENT_METHOD_WECHAT){
                        return '222';
                    }


                }
            }
        }
        return $order;
    }
}
