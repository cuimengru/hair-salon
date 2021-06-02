<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Designer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class DesignerController extends Controller
{
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
    public function disfavor(Designer $designer,Request $request)
    {
        $user = $request->user();
        $user->favoriteDesigners()->detach($designer);

        $data['message'] = "取消成功！";
        return response()->json($data, 200);
    }

    //收藏设计师列表
    public function followlist(Request $request)
    {
        $designer = $request->user()->favoriteDesigners()->paginate(6);
        foreach ($designer as $k=>$value){
            unset($designer[$k]['many_images']);
            unset($designer[$k]['rating']);
            unset($designer[$k]['certificate']);
            unset($designer[$k]['honor']);
            unset($designer[$k]['score']);
            unset($designer[$k]['is_recommend']);
            unset($designer[$k]['created_at']);
            unset($designer[$k]['updated_at']);
            unset($designer[$k]['pivot']);
        }
        return $designer;
    }

    //设计师详情
    public function show($Id, Request $request)
    {
        $designer = Designer::where('id','=',$Id)
            ->select('id','name','thumb','many_images','position','description','certificate','honor','score','label_id')
            ->first();
        if($designer['many_images']){
            foreach ($designer['many_images'] as $k=>$value){
                $many_imageUrl[$k] = Storage::disk('public')->url($value);
            }
            $designer['many_imageUrl'] = $many_imageUrl;
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
                    $render_imageUrl[$i] = Storage::disk('public')->url($image);
                }
                $designer['comments'][$c]['render_imageUrl'] = $render_imageUrl;
            }
        }
        return $designer;
    }

    //设计师列表
    public function index()
    {
        $designer = QueryBuilder::for(Designer::class)
            /*->allowedFilters([
                AllowedFilter::exact('type'), //商品类型 1集品 2自营 3闲置
                'title'
            ])*/
            ->where('is_employee','=',1)
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('id','name','position','thumb','label_id')
            ->paginate(6);

        return $designer;
    }
}
