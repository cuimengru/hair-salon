<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderPaid;
use App\Http\Controllers\Controller;
use App\Models\Designer;
use App\Models\Order;
use App\Models\ReserveOrder;
use App\Models\ServiceProject;
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
                    $this->afterPaid($order);
                    return $order;
                }else{
                    //余额不足，使用支付宝支付剩下的
                       /* if($payment_method == Order::PAYMENT_METHOD_ALIPAY){
                            $order['alipay'] = app('alipay')->app([
                                'out_trade_no'=>$order->no, //订单编号，需保证在商户端不重复
                                'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
                                'subject' => '支付商品的订单：'.$order->no,// 订单标题
                            ]);
                        }elseif ($payment_method == Order::PAYMENT_METHOD_WECHAT){
                            return '222';
                        }*/
                    $data['message'] = "余额不足，请选择其他支付方式!";
                    return response()->json($data, 403);

                }
            }

        }
        //支付宝支付
        if($payment_method == Order::PAYMENT_METHOD_ALIPAY){
            /*$order['alipay'] = app('alipay')->app([
                'out_trade_no'=>$order->no, //订单编号，需保证在商户端不重复
                'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
                'subject' => '支付商品的订单：'.$order->no,// 订单标题
            ]);*/
            $alipayorder = [
                'out_trade_no' => $order->no,
                'total_amount' => $order->total_amount,
                'subject'      => '支付商品的订单：'.$order->no,
            ];
            $order['datas'] = app('alipay')->app($alipayorder);
            //$out = json_decode($datas->getContent());
            /*echo $out;*/

            $order['alipay_id'] =$order['datas']->getContent();
            return $order;

        }
        //return $order;
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

    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }

    // 前端回调页面
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        try {
            app('alipay')->verify();
        } catch (\Exception $e){
            $e->getMessage();
        }
    }

    //服务器端回调
    public function alipayNotify()
    {
        // 校验输入参数
        $data = app('alipay')->verify();
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now('Asia/shanghai'), // 支付时间
            'payment_method' => 2, // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
            'status' => Order::STATUS_PAID,// 更新订单状态
        ]);
        $this->afterPaid($order);

        return app('alipay')->success();
    }

    //我的余额管理
    public function balance(Request $request)
    {
        $user = $request->user();

        //商品订单
        $products = Order::where('user_id','=',$user->id)
            ->with(['items.product'])
            ->where('payment_method','=','1')
            ->orwhere('refund_status','=',8)
            ->orderBy('updated_at', 'desc')
            ->select('id','total_amount','payment_method','refund_status','paid_at','created_at','updated_at')
            ->get();
        /*foreach ($products as $k=>$value){*/
            /*if($value['payment_method'] == 1 && $value['refund_status'] == 5){
                $products[$k]['status_text'] = "购物";
                $products[$k]['balance_text'] = "-".$value['total_amount'];
                $products[$k]['type_order'] = 1;
            }elseif ($value['payment_method'] == 1 && $value['refund_status'] == 7){
                $products[$k]['status_text'] = "购物";
                $products[$k]['balance_text'] = "-".$value['total_amount'];
                $products[$k]['type_order'] = 1;
            }elseif ($value['payment_method'] == 1 && $value['refund_status'] == 9){
                $products[$k]['status_text'] = "购物";
                $products[$k]['balance_text'] = "-".$value['total_amount'];
                $products[$k]['type_order'] = 1;
            }elseif ($value['refund_status'] == 8){
                $products[$k]['status_text'] = "退款";
                $products[$k]['balance_text'] = "+".$value['total_amount'];
                $products[$k]['type_order'] = 1;
            }*/
            /*foreach ($value['items'] as $p=>$product){
                $product_order[$k][$p] = $product;
                if($value['payment_method'] == 1 && $value['refund_status'] == 5){
                    $product_order[$k][$p]['status_text'] = "购物";
                    $product_order[$k][$p]['balance_text'] = "-".$product['price'] * $product['amount'];
                    $product_order[$k][$p]['type_order'] = 1;
                }elseif ($value['payment_method'] == 1 && $value['refund_status'] == 7){
                    $product_order[$k][$p]['status_text'] = "购物";
                    $product_order[$k][$p]['balance_text'] = "-".$product['price'] * $product['amount'];
                    $product_order[$k][$p]['type_order'] = 1;
                }elseif ($value['payment_method'] == 1 && $value['refund_status'] == 9){
                    $product_order[$k][$p]['status_text'] = "购物";
                    $product_order[$k][$p]['balance_text'] = "-".$product['price'] * $product['amount'];
                    $product_order[$k][$p]['type_order'] = 1;
                }elseif ($value['refund_status'] == 8){
                    $product_order[$k][$p]['status_text'] = "退款";
                    $product_order[$k][$p]['balance_text'] = "+".$product['price'] * $product['amount'];
                    $product_order[$k][$p]['type_order'] = 1;
                }
            }*/
        //}
        //$product_orders = array_reduce($product_order,'array_merge',array());

        //预约订单
        $reserves = ReserveOrder::where('user_id','=',$user->id)
            ->where('payment_method','=','1')
            ->where('type','=',1)
            ->orderBy('updated_at', 'desc')
            ->select('id','designer_id','service_project','money','payment_method','paid_at','created_at','updated_at')
            ->get();
        foreach ($reserves as $i=>$item){
            $reserves[$i]['status_text'] = "预约";
            $reserves[$i]['balance_text'] = "-¥".$item['money'];
            $reserves[$i]['type_order'] = 2;
            $designer = Designer::findOrFail($item['designer_id']);
            $reserves[$i]['designer_name'] = $designer->name;
            //$reserveOrder[$i]['designer_thumb'] = $designer->thumb_url;
            $service_project = ServiceProject::findOrFail($item['service_project']);
            $reserves[$i]['service_project_name'] = $service_project->name;
            $reserves[$i]['title'] = $designer->name.'('.$service_project->name.')';
        }

        $order_total = array_merge($products->toArray(),$reserves->toArray());
        //$order_total1 = array_merge($products->toArray(),$reserves->toArray());
        $order_total1 = array_column($order_total,'updated_at');
        array_multisort($order_total1,SORT_DESC,$order_total);
        //array_multisort($order_total1,SORT_DESC,$order_total);


        $order_totals['data'] = $order_total;

        $product_order = [];
        foreach ($order_totals['data'] as $o=>$order){
            if(!empty($order['items'])){
                foreach ($order['items'] as $p=>$product){
                    $product_order[$o][$p] = $product['product'];

                    if($order['payment_method'] == 1 && $order['refund_status'] == 5){
                        $product_order[$o][$p]['status_text'] = "购物";
                        $money = number_format($product['price'] * $product['amount'],2);
                        $product_order[$o][$p]['balance_text'] = "-¥".$money;
                        $product_order[$o][$p]['type_order'] = 1;
                        $product_order[$o][$p]['order_id'] = $product['order_id'];
                        $product_order[$o][$p]['order_created_at'] = $order['created_at'];
                        $product_order[$o][$p]['order_updated_at'] = $order['updated_at'];
                        $product_order[$o][$p]['paid_at'] = $order['paid_at'];
                    }elseif ($order['payment_method'] == 1 && $order['refund_status'] == 7){
                        $product_order[$o][$p]['status_text'] = "购物";
                        $money = number_format($product['price'] * $product['amount'],2);
                        $product_order[$o][$p]['balance_text'] = "-¥".$money;
                        $product_order[$o][$p]['type_order'] = 1;
                        $product_order[$o][$p]['order_id'] = $product['order_id'];
                        $product_order[$o][$p]['order_created_at'] = $order['created_at'];
                        $product_order[$o][$p]['order_updated_at'] = $order['updated_at'];
                        $product_order[$o][$p]['paid_at'] = $order['paid_at'];
                    }elseif ($order['payment_method'] == 1 && $order['refund_status'] == 9){
                        $product_order[$o][$p]['status_text'] = "购物";
                        $money = number_format($product['price'] * $product['amount'],2);
                        $product_order[$o][$p]['balance_text'] = "-¥".$money;
                        $product_order[$o][$p]['type_order'] = 1;
                        $product_order[$o][$p]['order_id'] = $product['order_id'];
                        $product_order[$o][$p]['order_created_at'] = $order['created_at'];
                        $product_order[$o][$p]['order_updated_at'] = $order['updated_at'];
                        $product_order[$o][$p]['paid_at'] = $order['paid_at'];
                    }elseif ($order['refund_status'] == 8){
                        $product_order[$o][$p]['status_text'] = "退款";
                        $money = number_format($product['price'] * $product['amount'],2);
                        $product_order[$o][$p]['balance_text'] = "+¥".$money;
                        $product_order[$o][$p]['type_order'] = 1;
                        $product_order[$o][$p]['order_id'] = $product['order_id'];
                        $product_order[$o][$p]['order_created_at'] = $order['created_at'];
                        $product_order[$o][$p]['order_updated_at'] = $order['updated_at'];
                        $product_order[$o][$p]['paid_at'] = $order['paid_at'];
                    }
                }
            }else{
                $product_order[$o] = $order;
            }

        }

        $product_orders = [];
        foreach ($product_order as $e=> $erwei){
            if(count($erwei) == count($erwei, 1)){
                $product_orders[] = $erwei;
            }else{
                foreach ($erwei as $m=>$mer){
                    $product_orders[] = $mer;
                }
            }
        }
        $count = count($product_orders); //总条数
        $page = $request->page;
        $pagesize = 5;
        $start=($page-1)*$pagesize;//偏移量，当前页-1乘以每页显示条数
        $product_orders1['data'] = array_slice($product_orders,$start,$pagesize);
        $product_orders1['total'] = $count;
        $product_orders1['current_page'] = $page;

        return $product_orders1;
    }

}
