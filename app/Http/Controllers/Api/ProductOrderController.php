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
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                        $orders[$k]['button_text'] = ['付款','取消订单'];
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
                            $orders[$k]['status_text'] = "退款失败";
                            $orders[$k]['button_text'] = ['退款失败','再次申请'];
                        }
                    }elseif ($value['ship_status'] == 2){ //已发货
                        $orders[$k]['status_text'] = "待收货";
                        //判断是否退款
                        if($value['refund_status'] == 5){
                            $orders[$k]['button_text'] = ['退款','查看物流','确认收货'];
                        }elseif ($value['refund_status'] == 7){
                            $orders[$k]['button_text'] = ['退款中','取消退款'];
                        }elseif ($value['refund_status'] == 8){
                            $orders[$k]['button_text'] = ['退款成功'];
                            $orders[$k]['status_text'] = "交易关闭";
                        }elseif ($value['refund_status'] == 9){
                            $orders[$k]['status_text'] = "退款失败";
                            $orders[$k]['button_text'] = ['退款失败','再次申请'];
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
//                hyh新增对设计师和项目是否存在的判断 如果存在findOrFail直接返回404
//                $ifdesigner = Designer::find($item['designer_id']);
//                if($ifdesigner){
//                $reserveOrder[$i]['designer_name']="";
//                $reserveOrder[$i]['designer_thumb']="";
                try{
                $designer = Designer::findOrFail($item['designer_id']);
                $reserveOrder[$i]['designer_name'] = $designer->name;
                $reserveOrder[$i]['designer_thumb'] = $designer->thumb_url;
                }catch(ModelNotFoundException $e)
                {
                    $reserveOrder[$i]['designer_name'] = "-设计师不存在-";
                    $reserveOrder[$i]['designer_thumb'] = "";
                }


//                $ifservice_project = Designer::find($item['service_project']);
//                if($ifservice_project){
                try{
                    $service_project = ServiceProject::findOrFail($item['service_project']);
                    $reserveOrder[$i]['service_project_name'] = $service_project->name;
                }catch(ModelNotFoundException $e){
                    $reserveOrder[$i]['service_project_name'] = "服务项目不存在-";
                }

                $reserveOrder[$i]['orderType'] = 2; //预约订单
                if($item['status'] == 1){
                    if($item['closed'] == false){
                        $reserveOrder[$i]['block_time'] = $item['created_at']->addSeconds(config('order.order_ttl'))->format('Y-m-d H:i:s');
                        $reserveOrder[$i]['status_text'] = "待付款";
                        $reserveOrder[$i]['button_text'] = ['付款','取消订单'];
                    }else{
                        $reserveOrder[$i]['block_time'] = 0;
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                        $reserveOrder[$i]['button_text'] = [];
                    }
                }elseif ($item['status'] == 3){
                    $reserveOrder[$i]['status_text'] = "预约成功";
                    if($item['refund_status'] == 5){
                        if($item['reviewed'] == false){
                            $now = Carbon::now('Asia/shanghai');
                            $day_now_time = $now->format('Y-m-d H:s');
                            if( $item['reserve_date'] <= $day_now_time || $item['ship_status'] == 1){
                                $reserveOrder[$i]['button_text'] = ['评价'];
                            }else{
                                $reserveOrder[$i]['button_text'] = ['修改时间'];
                            }
                            //$reserveOrder[$i]['button_text'] = ['修改时间','评价'];
                        }else{
                            $reserveOrder[$i]['button_text'] = ['已评价'];
                        }
                    }elseif($item['refund_status'] == 8){
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                        $reserveOrder[$i]['button_text'] = ['退款成功'];
                    }elseif($item['refund_status'] == 9){
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                        $reserveOrder[$i]['button_text'] = ['退款失败'];
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
//                $designer = Designer::findOrFail($item['designer_id']);
//                $reserveOrder[$i]['designer_name'] = $designer->name;
//                $reserveOrder[$i]['designer_thumb'] = $designer->thumb_url;
//                hyh新增对设计师和项目是否存在的判断 如果存在findOrFail直接返回404

//                $ifdesigner = Designer::find($item['designer_id']);
//                if($ifdesigner){
                try{
                $designer = Designer::findOrFail($item['designer_id']);
                $reserveOrder[$i]['designer_name'] = $designer->name;
                $reserveOrder[$i]['designer_thumb'] = $designer->thumb_url;
                }catch(ModelNotFoundException $e){
                    $reserveOrder[$i]['designer_name'] = "-设计师不存在-";
                    $reserveOrder[$i]['designer_thumb'] = "";
                }

//                $ifservice_project = Designer::find($item['service_project']);
//                if($ifservice_project){
                try{
                $service_project = ServiceProject::findOrFail($item['service_project']);
                $reserveOrder[$i]['service_project_name'] = $service_project->name;
                }catch(ModelNotFoundException $e){
                    $reserveOrder[$i]['service_project_name'] = "-服务项目不存在-";
                }

                $reserveOrder[$i]['orderType'] = 2; //预约订单
                if($item['status'] == 1){
                    if($item['closed'] == false){
                        $reserveOrder[$i]['block_time'] = $item['created_at']->addSeconds(config('order.order_ttl'))->format('Y-m-d H:i:s');
                        $reserveOrder[$i]['status_text'] = "待付款";
                        $reserveOrder[$i]['button_text'] = ['付款','取消订单'];
                    }else{
                        $reserveOrder[$i]['block_time'] = 0;
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                        $reserveOrder[$i]['button_text'] = [];
                    }
                }elseif ($item['status'] == 3){
                    $reserveOrder[$i]['status_text'] = "预约成功";
                    if($item['refund_status'] == 5){
                        if($item['reviewed'] == false){
                            $now = Carbon::now('Asia/shanghai');
                            $day_now_time = $now->format('Y-m-d H:s');
                            if( $item['reserve_date'] <= $day_now_time || $item['ship_status'] == 1){
                                $reserveOrder[$i]['button_text'] = ['评价'];
                            }else{
                                $reserveOrder[$i]['button_text'] = ['修改时间'];
                            }

                        }else{
                            $reserveOrder[$i]['button_text'] = ['已评价'];
                        }
                    }elseif($item['refund_status'] == 8){
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                        $reserveOrder[$i]['button_text'] = ['退款成功'];
                    }elseif($item['refund_status'] == 9){
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                        $reserveOrder[$i]['button_text'] = ['退款失败'];
                    }

                }
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
                ->where('closed','=',false)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($orders as $k=>$value){
                if($value['status'] == 1){
                    if($value['closed'] == false){
                        $orders[$k]['block_time'] = $value['created_at']->addSeconds(config('order.order_ttl'))->format('Y-m-d H:i:s');
                        $orders[$k]['status_text'] = "待付款";
                        $orders[$k]['button_text'] = ['付款','取消订单'];
                    }else{
                        $orders[$k]['block_time'] = 0;
                        $orders[$k]['status_text'] = "交易关闭";
                        $orders[$k]['button_text'] = [];
                    }
                }
                $orders[$k]['orderType'] = 1; //商品订单
            }

            $reserveOrder = ReserveOrder::where('user_id','=',$request->user()->id)
                ->where('type','=',1)
                ->where('status','=',1)
                ->where('closed','=',false)
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
                        $reserveOrder[$i]['button_text'] = ['付款','取消订单'];
                    }else{
                        $reserveOrder[$i]['block_time'] = 0;
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                        $reserveOrder[$i]['button_text'] = [];
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

        //待收货订单
        if($request->order_type == 4){
            $orders = Order::query()
                // 使用 with 方法预加载，避免N + 1问题
                ->with(['items.product','items.productSku'])
                ->where('user_id', $request->user()->id)
                ->where('status','=',3)
                ->whereIn('ship_status',[1,2])
                ->where('refund_status','=',5)
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->paginate(4);
            foreach ($orders as $k=>$value){
                //待发货
                if($value['ship_status'] == 1){
                    $orders[$k]['status_text'] = "待发货";
                    //判断是否退款
                    if($value['refund_status'] == 5){
                        $orders[$k]['button_text'] = ['退款'];
                    }/*elseif ($value['refund_status'] == 7){
                        $orders[$k]['button_text'] = ['退款中','取消退款'];
                    }elseif ($value['refund_status'] == 8){
                        $orders[$k]['button_text'] = ['退款成功'];
                        $orders[$k]['status_text'] = "交易关闭";
                    }elseif ($value['refund_status'] == 9){
                        $orders[$k]['status_text'] = "退款失败";
                        $orders[$k]['button_text'] = ['退款失败','再次申请'];
                    }*/
                }elseif ($value['ship_status'] == 2){ //已发货
                    $orders[$k]['status_text'] = "待收货";
                    //判断是否退款
                    if($value['refund_status'] == 5){
                        $orders[$k]['button_text'] = ['退款','查看物流','确认收货'];
                    }/*elseif ($value['refund_status'] == 7){
                        $orders[$k]['button_text'] = ['退款中','取消退款'];
                    }elseif ($value['refund_status'] == 8){
                        $orders[$k]['button_text'] = ['退款成功'];
                        $orders[$k]['status_text'] = "交易关闭";
                    }elseif ($value['refund_status'] == 9){
                        $orders[$k]['status_text'] = "退款失败";
                        $orders[$k]['button_text'] = ['退款失败','再次申请'];
                    }*/
                }
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
                if ($value['ship_status'] == 3){ //已收货
                    $orders[$k]['status_text'] = "交易成功";
                    if($value['reviewed'] == false){
                        $orders[$k]['button_text'] = ['评价'];
                    }else{
                        $orders[$k]['button_text'] = ['已评价'];
                    }
                }
                $orders[$k]['orderType'] = 1; //商品订单
            }

            $reserveOrder = ReserveOrder::where('user_id','=',$request->user()->id)
                ->where('type','=',1)
                ->where('reviewed','=',0)
                ->where('status','=',3)
                ->orwhere('ship_status','=',1)
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
                if ($item['status'] == 3){
                    $reserveOrder[$i]['status_text'] = "预约成功";
                    if($item['refund_status'] == 5){
                        if($item['reviewed'] == false){
                            $now = Carbon::now('Asia/shanghai');
                            $day_now_time = $now->format('Y-m-d H:s');
                            if( $item['reserve_date'] <= $day_now_time || $item['ship_status'] == 1){
                                $reserveOrder[$i]['button_text'] = ['评价'];
                            }else{
                                $reserveOrder[$i]['button_text'] = ['修改时间'];
                            }
                            //$reserveOrder[$i]['button_text'] = ['修改时间','评价'];
                        }else{
                            $reserveOrder[$i]['button_text'] = ['已评价'];
                        }
                    }
                }
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
        if($request->order_type == 6){
            $orders = Order::query()
                // 使用 with 方法预加载，避免N + 1问题
                ->with(['items.product','items.productSku'])
                ->where('user_id', $request->user()->id)
                ->where('reviewed','=',0)
                ->whereIn('status',[2,3])
                ->whereIn('refund_status',[7,8,9])
                ->orderBy('paid_at','desc')
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($orders as $k=>$value){
                if ($value['refund_status'] == 7){
                    $orders[$k]['status_text'] = "待商家处理";
                    $orders[$k]['button_text'] = ['退款中','取消退款'];
                }elseif ($value['refund_status'] == 8){
                    $orders[$k]['button_text'] = ['退款成功'];
                    $orders[$k]['status_text'] = "交易关闭";
                }elseif ($value['refund_status'] == 9){
                    $orders[$k]['status_text'] = "退款失败";
                    $orders[$k]['button_text'] = ['退款失败','再次申请'];
                }
                $orders[$k]['orderType'] = 1; //商品订单
            }

            $reserveOrder = ReserveOrder::where('user_id','=',$request->user()->id)
                ->where('type','=',1)
                ->where('reviewed','=',0)
                ->where('status','=',3)
                ->whereIn('refund_status',[7,8,9])
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
                    if($item['refund_status'] == 8){
                        $reserveOrder[$i]['button_text'] = ['退款成功'];
                        $reserveOrder[$i]['status_text'] = "交易关闭";
                    }elseif($item['refund_status'] == 9){
                        $orders[$k]['status_text'] = "待商家处理";
                        $orders[$k]['button_text'] = ['退款失败','查看原因'];
                    }

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

    }

    //某个商品订单详情
    public function show($orderId,Request $request)
    {
        $user = $request->user();
        $order = Order::where('id','=',$orderId)->where('user_id','=',$user->id)->first();
        $order->load(['items.productSku', 'items.product']);
        if($order['status'] == 1){
            if($order['closed'] == false){
                $order['block_time'] = $order['created_at']->addSeconds(config('order.order_ttl'))->format('Y-m-d H:i:s');
                $order['status_text'] = "待付款";
                $order['button_text'] = ['付款','取消订单'];
            }else{
                $order['block_time'] = 0;
                $order['status_text'] = "交易关闭";
                $order['button_text'] = [];
            }


        }elseif ($order['status'] == 3){
            //待发货
            if($order['ship_status'] == 1){
                $order['status_text'] = "待发货";
                //判断是否退款
                if($order['refund_status'] == 5){
                    $order['button_text'] = ['退款'];
                }elseif ($order['refund_status'] == 7){
                    $order['button_text'] = ['退款中','取消退款'];
                }elseif ($order['refund_status'] == 8){
                    $order['button_text'] = ['退款成功'];
                    $order['status_text'] = "交易关闭";
                }elseif ($order['refund_status'] == 9){
                    $order['status_text'] = "退款失败";
                    $order['button_text'] = ['退款失败','再次申请'];
                }
            }elseif ($order['ship_status'] == 2){ //已发货
                $order['status_text'] = "待收货";
                //判断是否退款
                if($order['refund_status'] == 5){
                    $order['button_text'] = ['退款','查看物流','确认收货'];
                }elseif ($order['refund_status'] == 7){
                    $order['button_text'] = ['退款中','取消退款'];
                }elseif ($order['refund_status'] == 8){
                    $order['button_text'] = ['退款成功'];
                    $order['status_text'] = "交易关闭";
                }elseif ($order['refund_status'] == 9){
                    $order['status_text'] = "退款失败";
                    $order['button_text'] = ['退款失败','再次申请'];
                }
            }elseif ($order['ship_status'] == 3){ //已收货
                $order['status_text'] = "交易成功";
                if($order['reviewed'] == false){
                    $order['button_text'] = ['评价'];
                }else{
                    $order['button_text'] = ['已评价'];
                }
            }

        }else{
            $order['block_time'] = 0;
        }
        return $order;
    }

    //商品退款
    public function refund($productId, Request $request)
    {
        $request->validate([
            'many_images' => 'array',
            //'video' => 'string'.$video,
        ]);
        $user = $request->user();
        $order = Order::where('id','=',$productId)->where('user_id','=',$user->id)->first();
        if(!$order){
            $data['message'] = "Without permission!";
            return response()->json($data, 403);
        }
        // 判断订单是否已付款
        if (!$order->paid_at) {
            $data['message'] = "该订单未支付，不可退款!";
            return response()->json($data, 403);
        }

        //判断订单退款状态是否正确
        if($order->refund_status == Order::REFUND_STATUS_PROCESSING){
            $data['message'] = "该订单已经申请过退款，请勿重复申请!";
            return response()->json($data, 403);
        }
        if($order->refund_status == Order::REFUND_STATUS_SUCCESS){
            $data['message'] = "该订单已经退款成功，请勿重复申请!";
            return response()->json($data, 403);
        }

        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra                  = $order->extra ?: [];
        //清空拒绝退款的理由
        if($order->refund_status == Order::REFUND_STATUS_FAILED){
            $extra['disagree_reason'] = null;
        }

        $extra['refund_reason'] = $request->reason;
        //合成数组
        $many_images = array($request->file('image_0'),$request->file('image_1'),$request->file('image_2'),$request->file('image_3'),$request->file('image_4'),$request->file('image_5'),$request->file('image_6'),$request->file('image_7'),$request->file('image_8'),$request->file('image_9'));

        if (!empty($many_images)) {
            foreach ($many_images as $k=>$value){
                if($value){
                    $image = upload_images($value, 'order', $user->id);
                    $attributes['many_images'][$k] = $image->path;
                }else{
                    $attributes['many_images'][$k] = null;
                }

            }
            $extra['many_images'] = array_filter($attributes['many_images']);
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PROCESSING,
                'extra'         => $extra,
            ]);

        }

        return $order;
    }

    //确认收货
    public function shipOrder($orderId, Request $request)
    {
        $user = $request->user();

        $order = Order::where('user_id','=',$user->id)
            ->where('id','=',$orderId)
            ->first();

        if(!$order){
            $data['message'] = "该订单不存在!";
            return response()->json($data, 403);
        }
        if($order->ship_status == 3){
            $data['message'] = "该订单已经确认过收货，无需重复!";
            return response()->json($data, 403);
        }
        $order->update(['ship_status' => 3]);

        $data['message'] = "确认收货成功!";
        return response()->json($data, 200);
        //return $order;
    }

    //取消退款
    public function refundOrder($orderId, Request $request)
    {
        $user = $request->user();

        $order = Order::where('user_id','=',$user->id)
            ->where('id','=',$orderId)
            ->first();

        if(!$order){
            $data['message'] = "该订单不存在!";
            return response()->json($data, 403);
        }

        if($order->refund_status == 5){
            $data['message'] = "该订单已经取消过退款，无需重复!";
            return response()->json($data, 403);
        }

        if($order->refund_status == 8){
            $data['message'] = "该订单已经退款成功，不能取消!";
            return response()->json($data, 403);
        }

        $order->update(['refund_status' => 5]);

        $data['message'] = "取消退款成功!";
        return response()->json($data, 200);
    }

    //查看物流
    public function logistics(Request $request)
    {
        $express_no = $request->express_no; //物流单号
        $express_company = $request->express_company; //物流公司
        error_reporting(E_ALL || ~E_NOTICE);
        $host = "https://wdexpress.market.alicloudapi.com";
        $path = "/gxali";
        $method = "GET";
        $appcode = "8a7010f3210e423898353b477c243d8c";//开通服务后 买家中心-查看AppCode
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        $querys = "n=".$express_no."&t=".$express_company;

        $bodys = "";
        $url = $host . $path . "?" . $querys;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $out_put = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        list($header, $body) = explode("\r\n\r\n", $out_put, 2);
        if ($httpCode == 200) {
            //print("正常请求计费(其他均不计费)<br>");
            //print($body);
            return $body;
        } else {
            if ($httpCode == 400 && strpos($header, "Invalid Param Location") !== false) {
                print("参数错误");
            } elseif ($httpCode == 400 && strpos($header, "Invalid AppCode") !== false) {
                print("AppCode错误");
            } elseif ($httpCode == 400 && strpos($header, "Invalid Url") !== false) {
                print("请求的 Method、Path 或者环境错误");
            } elseif ($httpCode == 403 && strpos($header, "Unauthorized") !== false) {
                print("服务未被授权（或URL和Path不正确）");
            } elseif ($httpCode == 403 && strpos($header, "Quota Exhausted") !== false) {
                print("套餐包次数用完");
            } elseif ($httpCode == 500) {
                print("API网关错误");
            } elseif ($httpCode == 0) {
                print("URL错误");
            } else {
                print("参数名错误 或 其他错误");
                print($httpCode);
                $headers = explode("\r\n", $header);
                $headList = array();
                foreach ($headers as $head) {
                    $value = explode(':', $head);
                    $headList[$value[0]] = $value[1];
                }
                print($headList['x-ca-error-message']);
            }
        }
        //return $querys;
    }

    //取消商品订单
    public function delete($orderId, Request $request)
    {
        $user = $request->user();

        $order = Order::where('id','=',$orderId)
            ->where('user_id','=',$user->id)
            ->first();
        if(!$order){
            $data['message'] = "该订单不存在!";
            return response()->json($data, 403);
        }

        if($order->status !=1){
            $data['message'] = "该订单无法取消!";
            return response()->json($data, 403);
        }

        $order->delete();
        $data['message'] = "该订单取消成功!";
        return response()->json($data, 200);
    }
}
