<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Designer;
use App\Models\ReserveInformation;
use App\Models\User;
use App\Models\UserLikeDesigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Str;

class DesignersController extends Controller
{
    //设计师列表
    public function index(Request $request)
    {
        $designer = QueryBuilder::for(Designer::class)
            /*->allowedFilters([
                AllowedFilter::exact('type'), //商品类型 1集品 2自营 3闲置
                'title'
            ])*/
            ->where('is_employee','=',1)
            ->defaultSort('-sort_list') //hyh设计师排序
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('id','name','position','thumb','label_id','sort_list')
            ->paginate(6);
        foreach ($designer as $k=>$value){

//            hyh 20211025 避免职位为空时，小程序里显示null的情况  接口:/designers/index
            if($designer[$k]['position']==''){
                $designer[$k]['position']="";
            }


            //收藏发型师
            if($request->user_id){
                $designer[$k]['follows'] = DB::table('user_favorite_designers')
                    ->where('user_id','=',$request->user_id)
                    ->where('designer_id','=',$value->id)
                    ->first();
                if ($designer[$k]['follows']){
                    $designer[$k]['follows_production'] = 1; //已收藏
            }else{
                    $designer[$k]['follows_production'] = 0; //未收藏
                }
                unset($designer[$k]['follows']);
            }else{
                $designer[$k]['follows_production'] = 0; //未收藏
            }
        }

        return $designer;
    }

    //设计师详情
    public function show($Id, Request $request)
    {
        $designer = Designer::where('id','=',$Id)
            ->select('id','name','thumb','many_images','position','description','certificate','honor','score','label_id')
            ->first();
        if(!empty($designer['many_images'])){
            foreach ($designer['many_images'] as $k=>$value){
                $many_imageUrl[$k] = Storage::disk('oss')->url($value);
            }
            $designer['many_imageUrl'] = $many_imageUrl;
        }
        if($designer['certificate'] == [null]){
            $designer['certificate'] = [];
        }

//        hyh换行
         $designer['description'] = Str::replace("\r\n", '<br/>',$designer['description']);

        if($designer['honor'] == [null]){
            $designer['honor'] = [];
        }
        $designer['comments'] = Comment::where('designer_id','=',$Id)
            ->where('type','=',1)
            ->where('status','=',1)
            ->orderBy('created_at','desc')
            ->select('id','user_id','rate','render_content','render_image','created_at')
            ->limit(2)->get();
        foreach ($designer['comments'] as $c=>$comment){
            $user = User::findOrFail($comment['user_id']);
            $designer['comments'][$c]['user_name'] = $user->nickname;
            $designer['comments'][$c]['user_avatar'] = $user->avatar_url;
            if ($comment['render_image']) {
                foreach ($comment['render_image'] as $i => $image) {
                    $render_imageUrl[$i] = Storage::disk('oss')->url($image);
                }
                $designer['comments'][$c]['render_imageUrl'] = $render_imageUrl;
            }
        }

        //用户是否收藏
        if($request->user_id){
            $favor = DB::table('user_favorite_designers')
                ->where('user_id','=',$request->user_id)
                ->where('designer_id','=',$Id)
                ->first();
            if($favor){
                $designer['favor_designer'] = 1; //已收藏
            }else{
                $designer['favor_designer'] = 0; //未收藏
            }

            //浏览记录
            $record = UserLikeDesigner::whereUserId($request->user_id)->whereDesignerId($Id)->first();
            if($record){
                $record->update([
                    'count' => $record->count + 1,
                ]);
            }else{
                UserLikeDesigner::create([
                    'user_id' => $request->user_id,
                    'designer_id' => $Id,
                    'type' => 3,
                ]);
            }
        }else{
            $designer['favor_designer'] = 0; //未收藏
        }

        $reserve = ReserveInformation::where('designer_id','=',$Id)->first();
        if($reserve){
            $designer['is_reserve'] = 1; //可以预约
        }else{
            $designer['is_reserve'] = 0; //不能预约
        }

        return $designer;
    }

    //收藏设计师
    public function favordesigner(Designer $designer,Request $request)
    {
        $user = $request->user();
        if ($user->favoriteDesigners()->find($designer->id)) {
            $data['message'] = " 已经收藏！";
            return response()->json($data, 403);
        }
        $user->favoriteDesigners()->attach($designer);

        $data['message'] = "收藏成功！";
        return response()->json($data, 200);
    }

    //取消收藏设计师
    public function disdesigner(Designer $designer,Request $request)
    {
        $user = $request->user();
        $user->favoriteDesigners()->detach($designer);

        $data['message'] = "取消成功！";
        return response()->json($data, 200);
    }

    //收藏发型师列表
    public function favorlist(Request $request)
    {
        $designers = $request->user()->favoriteDesigners()->paginate(6);
        foreach ($designers as $k=>$value){
            unset($designers[$k]['many_images']);
            unset($designers[$k]['rating']);
            unset($designers[$k]['certificate']);
            unset($designers[$k]['honor']);
            unset($designers[$k]['score']);
            unset($designers[$k]['is_recommend']);
            unset($designers[$k]['created_at']);
            unset($designers[$k]['updated_at']);
            unset($designers[$k]['pivot']);
        }
        return $designers;
    }
}

