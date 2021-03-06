<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReserveOrderRequest;
use App\Models\Designer;
use App\Models\Leavetime;
use App\Models\Product;
use App\Models\ReserveInformation;
use App\Models\ReserveOrder;
use App\Models\ServiceProject;
use App\Models\Worktime;
use App\Services\ReserveOrderService;
use App\Services\WorktimeService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReserveInformationController extends Controller
{
    public function __construct(WorktimeService $worktimeService)
    {
        $this->worktimeService = $worktimeService;
    }

    //工作时间
    public function worktime()
    {
        $worktime = Worktime::orderBy('order', 'asc')->select('id','time')->get();
        return $worktime;
    }

    //服务项目
    public function service()
    {
        $service = ServiceProject::select('id','name','price')->get();
        return $service;
    }

    //某个设计师工作时间
    public function day(Request $request)
    {
        $request->validate([
            'year' => 'required|numeric', // 年份
            'month' => 'required|numeric|between:1,12', // 月份
            //'day' => 'required|numeric|between:1,31', // 某天
        ]);

        $designerId = $request->designer_id; //设计师id
        // 处理错误的日期
        /*if (!checkdate($request->month, $request->day, $request->year)) {
            $data['message'] = "Date is wrong";
            return response()->json($data, 404);
        }*/
        $now = Carbon::now('Asia/shanghai');
        $now_day = $now->format('d');
        $day = $request->day ? $request->day : date('d');// 天
        $month = $request->month ? $request->month : date('m');// 月
        $year = $request->year ? $request->year : date('Y');// 年
        $m = $year . '-' . $month . '-' .$now_day;
        $start = Carbon::parse($m)->startOfMonth();
        $end = Carbon::parse($m)->endOfMonth()->addDays(13);
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            $can_choose = 0;
            $day = $date->format('d');
            $day_now = $now->format('d');
            $day_format = $date->format('Y-m-d');
            $day_now_format = $now->format('Y-m-d');
            $day_now_time = $now->format('Y-m-d H:s');
            $weekarray = array("周日","周一", "周二", "周三", "周四", "周五", "周六");
            $week = $weekarray[date("w",strtotime($day_format))];
            if ($day_now_format <= $day_format) {
                $can_choose = 1;
            }

            $leaveTime[$day] = Leavetime::where('designer_id','=',$designerId)->where('date','=',$day_format)->first(); //请假管理
            if($leaveTime[$day]){
                //半天假期
                if($leaveTime[$day]['type'] == 1){
                    $workTime['list'][$day]['time'] = Worktime::whereNotBetween('id',[$leaveTime[$day]['time'][0],$leaveTime[$day]['time'][1]])->select('id','time')->get();
                }else{
                    $workTime['list'][$day]['time'] = [];
                    $workTime['list'][$day]['day'] = $day;
                    $workTime['list'][$day]['week'] = $week;
                }
            }else{
                $workTime['list'][$day]['time'] = Worktime::orderBy('order', 'asc')->select('id','time')->get();
            }

            //订单管理
            if($workTime['list'][$day]['time']){
                foreach ($workTime['list'][$day]['time'] as $k=>$value){
                    /*$workTime['list'][$day]['time'][$k]['order'] = ReserveOrder::where('designer_id','=',$designerId)
                        ->where('date','=',$day_format)
                        ->where('time','=',$value['time'])
                        ->where('status','=',3)->first();*/

                    /*if(!empty($workTime['list'][$day]['time'][$k]['order'])){
                        $workTime['list'][$day]['time'][$k]['is_reserve'] = 0;//该时间点不能预约
                    }else{
                        $workTime['list'][$day]['time'][$k]['is_reserve'] = 1;//该时间点能预约
                    }*/
                    //unset($workTime['list'][$day]['time'][$k]['order']);
                    //时间
                    $time = $day_format . ' ' . $value['time'];


                    if($can_choose){
                        //$workTime['choose'][$day][$k]['can_choose'] = 1;//可以预约

                        //$workTime['list'][$day][$k]['is_reserve'] = 1;//该时间点能预约
                        if($day_now_time <= $time){
                            $workTime['list'][$day]['time'][$k]['order'] = ReserveOrder::where('designer_id','=',$designerId)
                                ->where('date','=',$day_format)
                                ->where('time','=',$value['time'])
                                ->where('status','=',3)->first();
                            //$workTime['list'][$day]['time'][$k]['can_choose'] = 1;
                            //$workTime['list'][$day]['time'][$k]['is_reserve'] = 1;
                            if(!empty($workTime['list'][$day]['time'][$k]['order'])){
                                $workTime['list'][$day]['time'][$k]['is_reserve'] = 0;//该时间点不能预约
                                $workTime['list'][$day]['time'][$k]['can_choose'] = 0;
                            }else{
                                $workTime['list'][$day]['time'][$k]['is_reserve'] = 1;//该时间点能预约
                                $workTime['list'][$day]['time'][$k]['can_choose'] = 1;
                            }
                            unset($workTime['list'][$day]['time'][$k]['order']);
                        }else{
                            $workTime['list'][$day]['time'][$k]['can_choose'] = 0;
                            $workTime['list'][$day]['time'][$k]['is_reserve'] = 0;
                        }
                    }else{
                        //$workTime['choose'][$day][$k]['can_choose'] = 0; //不能预约
                        $workTime['list'][$day]['time'][$k]['can_choose'] = 0;
                        $workTime['list'][$day]['time'][$k]['is_reserve'] = 0;//该时间点不能预约
                        unset($workTime['list']);
                    }
                }

                $workTime['list'][$day]['day'] = $day;
                $workTime['list'][$day]['week'] = $week;
                if($day_now_format > $day_format){
                    unset($workTime['list']);
                }

                //$workTime['list'][$day]['time1'] = [];
                //$workTime['list'][$day]['time2'] = [];

                /*if($workTime['list'][$day]['time']){
                    foreach ($workTime['list'][$day]['time'] as $i=>$item){
                        if($item->time <= '12:00'){
                            $workTime['list'][$day]['time1'] = $item;
                        }
                    }
                }*/
            }

        }

        $workTime['list'] = array_values($workTime['list']);
        //$workTime['choose'] = array_values($workTime['choose']);
        foreach ($workTime['list'] as $i=>$item){
            if(!empty($item['time'])){
                foreach ($item['time'] as $m=>$times){
                    if($times->time <= '12:00'){
                        $workTime['list'][$i]['time1'][$m] = $times;
                    }else{
                        $workTime['list'][$i]['time2'][$m] = $times;
                    }
                }
            }else{
                $workTime['list'][$i]['time1'] = [];
                $workTime['list'][$i]['time2'] = [];
            }
            unset($workTime['list'][$i]['time']);
            $workTime['list'][$i]['time2'] = array_values($workTime['list'][$i]['time2']);

            /*if($item['day'] < $day_now){
                unset($workTime['list'][0]);
            }*/
        }
        $workTime['list'] = array_values($workTime['list']);
        return $workTime;
    }

    //可预约的设计师列表  hyh app上，进入某个双色即是
    public function designerIndex(Request $request)
    {
        $designers = QueryBuilder::for(ReserveInformation::class)
            ->allowedFilters([
                AllowedFilter::exact('designer_id'), //设计师id  这个参数不要了！！AllowedFilter是非必填！！实际上不需要designer_id传进来！！
            ])
            ->orderBy('sort', 'desc') //hyh新增预约设计师列表排序
//            ->defaultSort('id') //按照创建时间排序 //hyh屏蔽
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('id','designer_id','service_project')
            ->paginate(99999);//hyhyh设计师列表不需要分页改造
        foreach ($designers as $k=>$value){
            //hyh推荐设计师排序 20210827修改
            $designer = Designer::where('id','=',$value['designer_id'])->first();
          $designers[$k]['designer_name'] = $designer->name;
            $designers[$k]['designer_thumb'] = $designer->thumb_url;

//            hyh 20211025 避免职位为空时，小程序里显示null的情况  接口:/api/v1/reserve/designer
             $designers[$k]['designer_position'] = $designer->position;
            if($designers[$k]['designer_position']==''){
               $designers[$k]['designer_position']="";
            }


            $designers[$k]['service'] = ServiceProject::whereIn('id',$value['service_project'])->select('id','name','price')->get();
        }

        return $designers;
    }

    //创建预约订单
    public function store(Request $request,ReserveOrderService $orderService)
    {
        $user = $request->user();

        return $orderService->store($user,$request);
    }

    //修改预约时间
    public function updateTime($orderId, Request $request)
    {
        $user = $request->user();

        $order = ReserveOrder::where('id','=',$orderId)->where('user_id','=',$user->id)->first();
        if(!$order){
            $data['message'] = '订单不存在！';
            return response()->json($data, 403);
        }
        $attributes = $request->only(['date','time']);

        $order->update($attributes);

        $data['message'] = '修改成功！';
        return response()->json($data, 200);
    }

    //某个预约订单详情
    public function show($orderId,Request $request)
    {
        $user = $request->user();
        $order = ReserveOrder::where('id','=',$orderId)
            ->where('user_id','=',$user->id)
            ->first();
        $designer = Designer::findOrFail($order->designer_id);
        $order['designer_name'] = $designer->name;
        $order['designer_thumb'] = $designer->thumb_url;
        $service_project = ServiceProject::findOrFail($order->service_project);
        $order['service_project_name'] = $service_project->name;
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
            $order['status_text'] = "预约成功";
            if($order['refund_status'] == 5){
                if($order['reviewed'] == false){
                    $now = Carbon::now('Asia/shanghai');
                    $day_now_time = $now->format('Y-m-d H:s');
                    if( $order['reserve_date'] <= $day_now_time || $order['ship_status'] == 1){
                        $order['button_text'] = ['评价'];
                    }else{
                        $order['button_text'] = ['修改时间'];
                    }
                }else{
                    $order['button_text'] = ['已评价'];
                }
            }

        }
        return $order;
    }

    //取消预约订单
    public function delete($orderId,Request $request)
    {
        $user = $request->user();

        $order = ReserveOrder::where('id','=',$orderId)
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
