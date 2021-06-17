<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductOrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Designer;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\ReserveOrder;
use App\Models\ServiceProject;
use App\Models\UserAddress;
use App\Services\ProductOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\QueryBuilder\QueryBuilder;

class ProductOrderController extends Controller
{
    //创建商品订单
    public function store(ProductOrderRequest $request, ProductOrderService $orderService)
    {
        $user = $request->user();

        $address = UserAddress::findOrFail($request->address_id);

        return $orderService->store($user,$address,$request->remark,$request->items);
    }

    //全部订单
    public function index(Request $request)
    {
        //全部订单
        if($request->order_type == 1){
            $orders = Order::query()
                // 使用 with 方法预加载，避免N + 1问题
                ->with(['items.product','items.productSku'])
                ->where('user_id', $request->user()->id)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($orders as $k=>$value){
                if($value['status'] == 1){
                    if($value['closed'] == false){
                        $orders[$k]['block_time'] = $value['created_at']->addSeconds(config('order.order_ttl'))->format('Y-m-d H:i:s');
                        $orders[$k]['status_text'] = "待付款";
                        $orders[$k]['button_text'] = ['付款'];
                    }else{
                        $orders[$k]['block_time'] = 0;
                        $orders[$k]['status_text'] = "交易关闭";
                        $orders[$k]['button_text'] = [];
                    }


                }elseif ($value['status'] == 3){
                    //待发货
                    if($value['ship_status'] == 1){
                        $orders[$k]['status_text'] = "待发货";
                        //判断是否退款
                        if($value['refund_status'] == 5){
                            $orders[$k]['button_text'] = ['退款'];
                        }elseif ($value['refund_status'] == 7){
                            $orders[$k]['button_text'] = ['退款中','取消退款'];
                        }elseif ($value['refund_status'] == 8){
                            $orders[$k]['button_text'] = ['退款成功'];
                            $orders[$k]['status_text'] = "交易关闭";
                        }elseif ($value['refund_status'] == 9){
                            $orders[$k]['button_text'] = ['退款失败','查看原因','再次申请'];
                        }
                    }elseif ($value['ship_status'] == 2){ //已发货
                        $orders[$k]['status_text'] = "待收货";
                        //判断是否退款
                        if($value['refund_status'] == 5){
                            $orders[$k]['button_text'] = ['退款','查看物流','确认收货'];
                        }elseif ($value['refund_status'] == 7){
                            $orders[$k]['button_text'] = ['退款中','取消退款','查看物流'];
                        }elseif ($value['refund_status'] == 8){
                            $orders[$k]['button_text'] = ['退款成功'];
                            $orders[$k]['status_text'] = "交易关闭";
                        }elseif ($value['refund_status'] == 9){
                            $orders[$k]['button_text'] = ['退款失败','查看原因','再次申请'];
                        }
                    }elseif ($value['ship_status'] == 3){ //已收货
                        $orders[$k]['status_text'] = "交易成功";
                        if($value['reviewed'] == false){
                            $orders[$k]['button_text'] = ['评价'];
                        }else{
                            $orders[$k]['button_text'] = ['已评价'];
                        }
                    }

                }else{
                    $orders[$k]['block_time'] = 0;
                }
                $orders[$k]['orderType'] = 1; //商品订单
            }

            $reserveOrder = ReserveOrder::where('user_id','=',$request->user()->id)
                ->where('type','=',1)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($reserveOrder as $i=>$item){
                $designer = Designer::findOrFail($item['designer_id']);
                $reserveOrder[$i]['designer_name'] = $designer->name;
                $reserveOrder[$i]['designer_thumb'] = $designer->thumb_url;
                $service_project = ServiceProject::findOrFail($item['service_project']);
                $reserveOrder[$i]['service_project_name'] = $service_project->name;
                $reserveOrder[$i]['orderType'] = 2; //预约订单
                if($item['status'] == 1){
                    if($item['closed'] == false){
                        $reserveOrder[$i]['block_time'] = $item['created_at']->addSeconds(config('order.order_ttl'))->format('Y-m-d H:i:s');
                        $reserveOrder[$i]['status_text'] = "待付款";
                        $reserveOrder[$i]['button_text'] = ['付款'];
                    }else{
                        $reserveOrder[$i]['block_time'] = 0;
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                        $reserveOrder[$i]['button_text'] = [];
                    }
                }elseif ($item['status'] == 3){
                    $reserveOrder[$i]['status_text'] = "预约成功";
                    if($item['refund_status'] == 5){
                        if($item['reviewed'] == false){
                            $reserveOrder[$i]['button_text'] = ['修改时间','评价'];
                        }else{
                            $reserveOrder[$i]['button_text'] = ['已评价'];
                        }
                    }

                }
            }

            $order_total = array_merge($orders->toArray(),$reserveOrder->toArray());
            $order_total1 = array_column($order_total,'created_at');
            array_multisort($order_total1,SORT_DESC,$order_total);

            $count = count($order_total); //总条数
            $page = $request->page;
            $pagesize = 4;
            $start=($page-1)*$pagesize;//偏移量，当前页-1乘以每页显示条数
            $order_totals['data'] = array_slice($order_total,$start,$pagesize);
            $order_totals['total'] = $count;
            $order_totals['current_page'] = $page;
            return $order_totals;
        }

        //我的预约订单
        if($request->order_type == 2){
            $reserveOrder = QueryBuilder::for(ReserveOrder::class)
                ->where('user_id','=',$request->user()->id)
                ->where('type','=',1)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->paginate(3);
            foreach ($reserveOrder as $i=>$item){
                $designer = Designer::findOrFail($item['designer_id']);
                $reserveOrder[$i]['designer_name'] = $designer->name;
                $reserveOrder[$i]['designer_thumb'] = $designer->thumb_url;
                $service_project = ServiceProject::findOrFail($item['service_project']);
                $reserveOrder[$i]['service_project_name'] = $service_project->name;
                $reserveOrder[$i]['orderType'] = 2; //预约订单
            }

            return $reserveOrder;
        }

        //待付款订单
        if($request->order_type == 3){
            $orders = Order::query()
                // 使用 with 方法预加载，避免N + 1问题
                ->with(['items.product','items.productSku'])
                ->where('user_id', $request->user()->id)
                ->where('status','=',1)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->paginate(4);
            foreach ($orders as $k=>$value){
                if($value['status'] == 1){
                    $orders[$k]['block_time'] = $value['created_at']->addSeconds(config('order.order_ttl'))->format('Y-m-d H:i:s');
                }else{
                    $orders[$k]['block_time'] = 0;
                }
                $orders[$k]['orderType'] = 1; //商品订单
            }

            return $orders;
        }

        //待收货订单
        if($request->order_type == 4){
            $orders = Order::query()
                // 使用 with 方法预加载，避免N + 1问题
                ->with(['items.product','items.productSku'])
                ->where('user_id', $request->user()->id)
                ->where('status','=',3)
                ->where('ship_status','=',2)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->paginate(4);
            foreach ($orders as $k=>$value){
                /*if($value['status'] == 1){
                    $orders[$k]['block_time'] = $value['created_at']->addSeconds(config('order.order_ttl'))->format('H:i:s');
                }else{
                    $orders[$k]['block_time'] = 0;
                }*/
                $orders[$k]['orderType'] = 1; //商品订单
            }

            return $orders;
        }

        //待评价的订单
        if($request->order_type == 5){
            $orders = Order::query()
                // 使用 with 方法预加载，避免N + 1问题
                ->with(['items.product','items.productSku'])
                ->where('user_id', $request->user()->id)
                ->where('reviewed','=',0)
                ->where('status','=',3)
                ->where('ship_status','=',3)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($orders as $k=>$value){
                /*if($value['status'] == 1){
                    $orders[$k]['block_time'] = $value['created_at']->addSeconds(config('order.order_ttl'))->format('H:i:s');
                }else{
                    $orders[$k]['block_time'] = 0;
                }*/
                $orders[$k]['orderType'] = 1; //商品订单
            }

            $reserveOrder = ReserveOrder::where('user_id','=',$request->user()->id)
                ->where('type','=',1)
                ->where('reviewed','=',0)
                ->where('status','=',3)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($reserveOrder as $i=>$item){
                $designer = Designer::findOrFail($item['designer_id']);
                $reserveOrder[$i]['designer_name'] = $designer->name;
                $reserveOrder[$i]['designer_thumb'] = $designer->thumb_url;
                $service_project = ServiceProject::findOrFail($item['service_project']);
                $reserveOrder[$i]['service_project_name'] = $service_project->name;
                $reserveOrder[$i]['orderType'] = 2; //预约订单
            }

            $order_total = array_merge($orders->toArray(),$reserveOrder->toArray());
            $order_total1 = array_column($order_total,'updated_at');
            array_multisort($order_total1,SORT_DESC,$order_total);

            $count = count($order_total); //总条数
            $page = $request->page;
            $pagesize = 4;
            $start=($page-1)*$pagesize;//偏移量，当前页-1乘以每页显示条数
            $order_totals['data'] = array_slice($order_total,$start,$pagesize);
            $order_totals['total'] = $count;
            $order_totals['current_page'] = $page;
            return $order_totals;
        }

        //退款售后
        /*if($request->order_type == 6){

        }*/

    }

    //某个商品订单详情
    public function show($orderId,Request $request)
    {
        $user = $request->user();
        $order = Order::where('id','=',$orderId)->where('user_id','=',$user->id)->first();
        $order->load(['items.productSku', 'items.product']);
        return $order;
    }


}
