<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Culture;
use App\Models\Fashion;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CultureController extends Controller
{
    //文教娱乐列表
    public function index(Request $request)
    {
        $cultures = QueryBuilder::for(Culture::class)
            ->allowedFilters([
                AllowedFilter::exact('place_id'), //位置：1教育2培训3线下活动
            ])
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('start_time','updated_at') // 支持排序字段 开始时间 更新时间
            ->select('id','title','thumb','start_time')
            ->paginate(3);
        foreach ($cultures as $k=>$value){
            $cultures[$k]['start_time'] = date("Y.m.d", strtotime($value['start_time']));
        }
        return $cultures;
    }

    //某个详情
    public function show($Id,Request $request)
    {
        $cultures = Culture::where('id','=',$Id)
            ->select('id','title','thumb','teacher','description','content','start_time')
            ->first();
        $cultures['start_time'] = date("Y.m.d", strtotime($cultures['start_time']));
        return $cultures;
    }

    //时尚资讯列表
    public function fashionIndex(Request $request)
    {
        $fashions= Fashion::where('is_recommend','=',1)
            ->orderBy('order','asc')
            ->orderBy('created_at','desc')
            ->select('id','title','thumb','description','created_at','updated_at')
            ->paginate(8);
        foreach ($fashions as $k=>$value){
            $fashions[$k]['created_time'] = date("Y.m.d", strtotime($value['created_at']));
            $fashions[$k]['updated_time'] = date("Y.m.d", strtotime($value['updated_at']));
        }
        return $fashions;
    }

    //资讯详情
    public function fashionShow($Id,Request $request)
    {
        $fashions = Fashion::where('id','=',$Id)
            ->select('id','title','thumb','video','description','content','created_at','updated_at')
            ->first();
        $fashions['created_time'] = date("Y.m.d", strtotime($fashions['created_at']));
        $fashions['updated_time'] = date("Y.m.d", strtotime($fashions['updated_at']));
        return $fashions;
    }
}
