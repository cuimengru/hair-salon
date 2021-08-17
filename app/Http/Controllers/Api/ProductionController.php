<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\Designer;
use App\Models\Fashion;
use App\Models\Production;
use App\Models\ProductionAge;
use App\Models\UserLikeDesigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductionController extends Controller
{
    //作品首页
    public function index(Request $request)
    {
        //广告banner
        $index = [];

        $index['advert'] = Advert::where('category_id','=',6)->orderBy('order', 'asc')->select('id','thumb', 'url')->get();

        //作品$index['production']
         $productions= Production::where('is_recommend','=',1)
             ->where('on_sale','=',1)
             ->orderBy('sort','desc')//hyh推荐作品排序
             ->orderBy('created_at','desc')
            ->select('id','title','thumb','type','video','sort')
            ->get();
         foreach ($productions as $p=>$product){
             //收藏作品
             if($request->user_id){
                 $productions[$p]['follows'] = DB::table('user_favorite_productions')
                     ->where('user_id','=',$request->user_id)
                     ->where('production_id','=',$product->id)
                     ->first();
                 if ($productions[$p]['follows']){
                     $productions[$p]['follows_production'] = 1; //已收藏
                 }else{
                     $productions[$p]['follows_production'] = 0; //未收藏
                 }
                 unset($productions[$p]['follows']);
             }else{
                 $productions[$p]['follows_production'] = 0; //未收藏
             }
             $index1['production'] = $productions;
         }

//        hyhmodelname
        $index['production']['modelname'] = config('modelname.productions');
        $index['production']['list'] = $index1['production'];


        //设计师$index['designers']
        $index1['designers'] = Designer::where('is_recommend','=',1)
            ->where('is_employee','=',1)
            ->orderBy('sort','desc')//hyh推荐设计师排序
            ->orderBy('created_at','desc')
            ->select('id','name','thumb','description','position','label_id')->get();
         //收藏设计师
        foreach ($index1['designers'] as $d=>$designer){
            //收藏设计师
            if($request->user_id){
                $index1['designers'][$d]['follows'] = DB::table('user_favorite_designers')
                    ->where('user_id','=',$request->user_id)
                    ->where('designer_id','=',$designer->id)
                    ->first();
                if($index1['designers'][$d]['follows']){
                    $index1['designers'][$d]['follows_designer'] = 1; //已收藏
                }else{
                    $index1['designers'][$d]['follows_designer'] = 0; //未收藏
                }
                unset($index1['designers'][$d]['follows']);
            }else{
                $index1['designers'][$d]['follows_designer'] = 0; //未收藏
            }
        }
//        hyhmodelname
        $index['designers']['modelname'] = config('modelname.designers');
        $index['designers']['list'] = $index1['designers'];


//   资讯
        $index1['fashions'] = Fashion::where('is_recommend','=',1)
            ->orderBy('order','asc')
            ->orderBy('created_at','desc')
            ->select('id','title','thumb','description','created_at','updated_at')
            ->paginate(4);
        foreach ($index1['fashions'] as $k=>$value){
            $index1['fashions'][$k]['created_time'] = date("Y.m.d", strtotime($value['created_at']));
            $index1['fashions'][$k]['updated_time'] = date("Y.m.d", strtotime($value['updated_at']));
        }

//        hyhmodelname
        $index['fashions']['modelname'] = config('modelname.fashions');
        $index['fashions']['list'] = $index1['fashions'];

        return $index;
    }

    //作品详情
    public function show($Id, Request $request)
    {
        $production = Production::where('id','=',$Id)
            ->select('title','description','content','thumb','video','many_images')
            ->first();
        if($production['many_images']){
            foreach ($production['many_images'] as $k=>$value){
                $many_imageUrl[$k] = Storage::disk('oss')->url($value);
            }
            $production['many_imageUrl'] = $many_imageUrl;
        }
        if($request->user_id){
            $production['follows'] = DB::table('user_favorite_productions')
                ->where('user_id','=',$request->user_id)
                ->where('production_id','=',$Id)
                ->first();
            if ($production['follows']){
                $production['follows_production'] = 1; //已收藏
            }else{
                $production['follows_production'] = 0; //未收藏
            }
            unset($production['follows']);
            $record = UserLikeDesigner::whereUserId($request->user_id)->whereProductionId($Id)->first();
            if($record){
                $record->update([
                    'count' => $record->count + 1,
                ]);
            }else{
                UserLikeDesigner::create([
                    'user_id' => $request->user_id,
                    'production_id' => $Id,
                    'type' => 4,
                ]);
            }


        }else{
            $production['follows_production'] = 0; //未收藏
        }
        //浏览次数
        $record = UserLikeDesigner::where('production_id','=',$Id)->count();
        $production_total = Production::where('id','=',$Id)->first();
        $production_total->update([
            'rating' => $record,
        ]);

        return $production;
    }

    //收藏作品
    public function favor(Production $production,Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProductions()->find($production->id)) {
            $data['message'] = " 已经收藏！";
            return response()->json($data, 403);
        }
        $user->favoriteProductions()->attach($production);

        $data['message'] = "收藏成功！";
        return response()->json($data, 200);
    }

    //取消收藏作品
    public function disfavor(Production $production,Request $request)
    {
        $user = $request->user();
        $user->favoriteProductions()->detach($production);

        $data['message'] = "取消成功！";
        return response()->json($data, 200);
    }

    //收藏作品列表
    public function followlist(Request $request)
    {
        $production = $request->user()->favoriteProductions()->paginate(9);
        foreach ($production as $k=>$value){
            unset($production[$k]['many_images']);
            //unset($production[$k]['video']);
            unset($production[$k]['description']);
            unset($production[$k]['content']);
            unset($production[$k]['rating']);
            unset($production[$k]['is_recommend']);
            unset($production[$k]['created_at']);
            unset($production[$k]['updated_at']);
            unset($production[$k]['pivot']);
        }
        return $production;
    }

    //全部作品列表
    public function allIndex(Request $request)
    {
        $productions = QueryBuilder::for(Production::class)
            ->allowedFilters([
                AllowedFilter::exact('gender'), //性别
                //AllowedFilter::exact('age_id'), //年龄段
//                AllowedFilter::exact('length_id'), //长度
//                AllowedFilter::exact('color_id'), //发质
//                AllowedFilter::exact('height_id'), //身高 //hyh身高改多选
//                AllowedFilter::exact('face_id'), //脸型id
//                AllowedFilter::exact('project_id'), //项目id
                //AllowedFilter::exact('hair_id'), //烫染id
                'style_id',
                'age_id',
                'hair_id',
                'height_id',//hyh身高改多选
                'color_id',
                'length_id',
                'face_id',
                'project_id'
            ])
            ->where('on_sale','=',1)
            ->where('is_recommend','=',0)//hyh客户要求，列表页不显示推荐的作品。
            ->defaultSort('-sort_list') //hyh作品排序
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('id','title','thumb','type','video','sort_list')
            ->paginate(15);
        foreach ($productions as $p=>$product){
            //收藏作品
            if($request->user_id){
                $productions[$p]['follows'] = DB::table('user_favorite_productions')
                    ->where('user_id','=',$request->user_id)
                    ->where('production_id','=',$product->id)
                    ->first();
                if ($productions[$p]['follows']){
                    $productions[$p]['follows_production'] = 1; //已收藏
                }else{
                    $productions[$p]['follows_production'] = 0; //未收藏
                }
                unset($productions[$p]['follows']);
            }else{
                $productions[$p]['follows_production'] = 0; //未收藏
            }
            $index['production'] = $productions;
        }

        return $productions;
    }

}
