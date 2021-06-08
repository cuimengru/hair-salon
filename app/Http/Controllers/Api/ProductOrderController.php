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
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product','items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($orders as $k=>$value){
            if($value['status'] == 1){
                $orders[$k]['block_time'] = $value['created_at']->addSeconds(config('order.order_ttl'))->format('H:i:s');
            }else{
                $orders[$k]['block_time'] = 0;
            }
        }

        $reserveOrder = ReserveOrder::where('user_id','=',$request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($reserveOrder as $i=>$item){
            $designer = Designer::findOrFail($item['designer_id']);
            $reserveOrder[$i]['designer_name'] = $designer->name;
            $reserveOrder[$i]['designer_thumb'] = $designer->thumb_url;
            $service_project = ServiceProject::findOrFail($item['service_project']);
            $reserveOrder[$i]['service_project_name'] = $service_project->name;
        }

        $order_total = array_merge($orders->toArray(),$reserveOrder->toArray());

        $count = count($order_total); //总条数
        $page = $request->page;
        $pagesize = 4;
        $start=($page-1)*$pagesize;//偏移量，当前页-1乘以每页显示条数
        $order_totals['data'] = array_slice($order_total,$start,$pagesize);
        $order_totals['total'] = $count;
        $order_totals['current_page'] = $page;
        return $order_totals;
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
