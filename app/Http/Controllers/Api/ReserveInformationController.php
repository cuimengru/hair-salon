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
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ReserveInformationController extends Controller
{
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
            'day' => 'required|numeric|between:1,31', // 某天
        ]);

        $designerId = $request->designer_id; //设计师id
        // 处理错误的日期
        if (!checkdate($request->month, $request->day, $request->year)) {
            $data['message'] = "Date is wrong";
            return response()->json($data, 404);
        }
        $day = $request->day ? $request->day : date('d');// 天
        $month = $request->month ? $request->month : date('m');// 月
        $year = $request->year ? $request->year : date('Y');// 年
        $day_format = $year . '-' . $month . '-' . $day;

        $leaveTime = Leavetime::where('designer_id','=',$designerId)->where('date','=',$day_format)->first(); //请假管理
        if($leaveTime){
            //半天假期
            if($leaveTime['type'] == 1){
                $workTime = Worktime::whereNotBetween('id',[$leaveTime['time'][0],$leaveTime['time'][1]])->select('id','time')->get();
            }else{
                $workTime = [];
            }
        }else{
            $workTime = Worktime::orderBy('order', 'asc')->select('id','time')->get();
        }

        //订单管理
        //$orders = ReserveOrder::where('designer_id','=',$designerId)->where('time','=',$day_format)->where('status','=',3)->get();
        /*if($orders){
            foreach ($orders as $k=>$value){
                $time = date("H:i", strtotime($value['time']));
                $order[$k]['reserve'] = Worktime::where('time','=',$time)->first();
                if($order[$k]['reserve']){
                    $order[$k]['is_reserve'] = 1;
                }else{
                    $order[$k]['is_reserve'] = 0;
                }
            }
        }*/
        //$workTime['is_reserve'] = $order;

        return $workTime;
    }

    //可预约的设计师列表
    public function designerIndex(Request $request)
    {
        $designers = QueryBuilder::for(ReserveInformation::class)
            ->allowedFilters([
                AllowedFilter::exact('designer_id'), //设计师id
            ])
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('id','designer_id','service_project')
            ->paginate(3);
        foreach ($designers as $k=>$value){
            $designer = Designer::where('id','=',$value['designer_id'])->first();
            $designers[$k]['designer_name'] = $designer->name;
            $designers[$k]['designer_thumb'] = $designer->thumb_url;
            $designers[$k]['designer_position'] = $designer->position;
            $designers[$k]['service'] = ServiceProject::whereIn('id',$value['service_project'])->select('id','name','price')->get();
        }

        return $designers;
    }

    //提交预约订单
    public function store(ReserveOrderRequest $request,ReserveOrderService $orderService)
    {

    }


}
