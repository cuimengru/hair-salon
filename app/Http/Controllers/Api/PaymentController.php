<?php

namespace App\Http\Controllers\Api;

use App\Events\OrderPaid;
use App\Events\ReserveOrderPaid;
use App\Events\UserOrderPaid;
use App\Http\Controllers\Controller;
use App\Models\BalanceRecord;
use App\Models\Designer;
use App\Models\Order;
use App\Models\ReserveOrder;
use App\Models\ServiceProject;
use App\Models\User;
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
            $data['message'] = "未经许可!";
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
                    //$user->original_balance = $user->balance;
                    $user->update();
                    // 更新订单付款信息
                    $order->update([
                        'balance'=>$user->balance, //原余额
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
                        'balance'=>$user->balance, //原余额
                        'total_amount' => $balance,
                        'status' => ReserveOrder::STATUS_PAID,// 更新订单状态
                        'payment_method' => $payment_id,
                        'paid_at'=>Carbon::now('Asia/shanghai'),
                    ]);
                    return $order;
                }else{
                    //余额不足
                    $data['message'] = "余额不足，请选择其他支付方式!";
                    return response()->json($data, 403);
                }
            }
        }
        //支付宝支付
        if($payment_method == Order::PAYMENT_METHOD_ALIPAY){
            $alipayorder = [
                'out_trade_no' => $order->no,
                'total_amount' => $order->money,
                'subject'      => '支付预约的订单：'.$order->no,
            ];
            $order['datas'] = app('reservealipay')->app($alipayorder);
            //$out = json_decode($datas->getContent());
            /*echo $out;*/

            $order['alipay_id'] =$order['datas']->getContent();
            return $order;
        }
        if ($payment_method == Order::PAYMENT_METHOD_WECHAT){
            return '222';
        }

    }

    // 商品前端回调页面
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        /*try {
            app('alipay')->verify();
        } catch (\Exception $e){
            $e->getMessage();
        }*/
        $data = app('alipay')->verify();

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
    }

    //商品服务器端回调
    public function alipayNotify()
    {
        // 校验输入参数
        $alipay = app('alipay');
        try {
            $data = $alipay->verify();
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

        }catch (\Exception $e) {
            // $e->getMessage();
        }

        return app('alipay')->success();
    }

    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }

    //预约订单支付宝前端回调
    public function reserveReturn()
    {
        $data = app('reservealipay')->verify();
    }

    //预约订单支付宝前端回调
    public function reserveNotify()
    {
        $alipay = app('reservealipay');
        try {
            $data = $alipay->verify();
            if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                return app('reservealipay')->success();
            }
            // $data->out_trade_no 拿到订单流水号，并在数据库中查询
            $order = ReserveOrder::where('no', $data->out_trade_no)->first();
            // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
            if (!$order) {
                return 'fail';
            }
            // 如果这笔订单的状态已经是已支付
            if ($order->paid_at) {
                // 返回数据给支付宝
                return app('reservealipay')->success();
            }

            $order->update([
                'paid_at'        => Carbon::now('Asia/shanghai'), // 支付时间
                'payment_method' => 2, // 支付方式
                'payment_no'     => $data->trade_no, // 支付宝订单号
                'status' => ReserveOrder::STATUS_PAID,// 更新订单状态
            ]);
            $this->reserveAfterPaid($order);

        }catch (\Exception $e) {
            // $e->getMessage();
        }

        return app('alipay')->success();
    }

    protected function reserveAfterPaid(ReserveOrder $order)
    {
        event(new ReserveOrderPaid($order));
    }
    //我的余额管理
    public function balance(Request $request)
    {
        $user = $request->user();

        //商品订单
        $products = Order::where('user_id','=',$user->id)
            ->with(['items.product'])
            //->whereOr('payment_method','=',1)
            //->whereOr('refund_status','=',8)
            ->whereNotIn('payment_method',['2,3,null'])
            ->whereNotIn('refund_status',['6','7','9'])
            ->orderBy('updated_at', 'desc')
            ->select('id','total_amount','payment_method','refund_status','paid_at','created_at','updated_at')
            ->get();
        
        //预约订单
        $reserves = ReserveOrder::where('user_id','=',$user->id)
            ->where('payment_method','=',1)
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

    //充值
    public function balanceStore(Request $request)
    {
        $user = $request->user();

        $payment_method = $request->payment_method; //支付方式

        //支付宝支付
        if($payment_method == User::PAYMENT_METHOD_ALIPAY){
            $alipayorder = [
                'out_trade_no' => $user->phone.time(),
                'total_amount' => $request->balance,
                'subject'      => '充值余额的订单：'.$user->phone.time(),
            ];
            $user['datas'] = app('balancealipay')->app($alipayorder);

            $user['alipay_id'] =$user['datas']->getContent();
            return $user;
        }
    }

    //余额充值支付宝前端回调
    public function balanceReturn()
    {
        $data = app('balancealipay')->verify();
    }

    //余额充值支付宝服务器回调
    public function balanceNotify()
    {
        $alipay = app('balancealipay');
        try {
            $data = $alipay->verify();
            if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                return app('balancealipay')->success();
            }
            // $data->out_trade_no 拿到订单流水号，并在数据库中查询
            $phone = substr($data->out_trade_no,0,11);
            $user = User::where('phone', $phone)->first();

            //创建充值记录
            BalanceRecord::create([
                'user_id' => $user->id,
                'paid_at'        => Carbon::now('Asia/shanghai'), // 支付时间
                'payment_method' => 2, // 支付方式
                'payment_no'     => $data->trade_no, // 支付宝订单号
                'total_amount' => $data->total_amount,
                'original_balance' => $user->balance,
                'no' => $data->out_trade_no,
            ]);
            // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
            if (!$user) {
                return 'fail';
            }
            $balance_jilu = BalanceRecord::where('no', $data->out_trade_no)->first();
            // 如果这笔订单的状态已经是已支付
            /*if ($balance_jilu->paid_at) {
                // 返回数据给支付宝
                return app('reservealipay')->success();
            }*/

            $balance = $balance_jilu->total_amount + $user->balance;

            $user->update([
                'balance' => $balance,
                //'original_balance' => $balance,
            ]);

            $this->balanceAfterPaid($user);

        }catch (\Exception $e) {
            // $e->getMessage();
        }

        return app('alipay')->success();
    }

    public function balanceAfterPaid(User $user)
    {
        event(new UserOrderPaid($user));
    }
}
