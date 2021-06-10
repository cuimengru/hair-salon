<?php


namespace App\Services;


use App\Models\Designer;
use App\Models\Leavetime;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\String_;

class WorktimeService
{
    //按照月份查询
    public function month(int $designerId,Request $request)
    {
        $month = $request->month ? $request->month : date('m');// 月
        $year = $request->year ? $request->year : date('Y');// 年
        // 获取指定月份的每一天
        $m = $year . '-' . $month;
        $start = Carbon::parse($m)->startOfMonth();
        $end = Carbon::parse($m)->endOfMonth();
        $period = CarbonPeriod::create($start, $end);
        $now = Carbon::now('Asia/shanghai');
        foreach ($period as $date) {
            $day = $date->format('d');
            $day_format = $date->format('Y-m-d');
            $day_now_format = $now->format('Y-m-d');

            //查询发型师某天是否请假
            $leaveTime = Leavetime::where('designer_id','=',$designerId)->where('date','=',$day_format)->first(); //请假管理
        }
        return $leaveTime;
    }

    //查询发型师某天是否请假
    /*public function checkLeavetime(int $designerId,string $day_format)
    {
        $leaveTime = Leavetime::where('designer_id','=',$designerId)->where('date','=',$day_format)->first(); //请假管理
        return $leaveTime;
    }*/
}
