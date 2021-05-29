<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\Community;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CommunityController extends Controller
{
    //发布社区内容
    public function store(Request $request)
    {
        $user = $request->user();
        //$video = video_ext();

        $request->validate([
            'title' => 'required|string|min:4',
            'contents' => 'required|string',
            'many_images' => 'array',
            //'video' => 'string'.$video,
        ]);
        if ($request->file('many_images')) {

            foreach ($request->file('many_images') as $k=>$value){
                $image = upload_images($value, 'feedback', $user->id);
                $attributes['many_images'][$k] = $image->path;
                //$avatar_image_id = array($image->id);
            }

            $community = Community::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'content' => $request->contents,
                'many_images' => $attributes['many_images'],
            ]);

        }else if($request->file('video')){

            $video = get_vimeo_mp4($request->file('video'), 'community');

            $community = Community::create([
            'user_id'=>$user->id,
            'title' => $request->title,
            'content' => $request->contents,
            'video' => $video,
            ]);
        }

        $data['message'] = "发布成功！";
        return response()->json($data, 200);
    }

    //社区列表
    public function index(Request $request)
    {
        $community['banner'] = Advert::where('category_id','=',7)->orderBy('order', 'asc')->select('id','thumb', 'url')->get();
        $community['community'] = QueryBuilder::for(Community::class)
            /*->allowedFilters([
                AllowedFilter::exact('type') //商品类型 1集品 2自营 3闲置
            ])*/
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->where('status','=',1)
            ->select('id','user_id','title','content','many_images','video','created_at')
            ->paginate(3);
            foreach ($community['community'] as $k=>$value){
                if($value['many_images']){
                  foreach ($value['many_images'] as $i=>$image){
                      $many_imageUrl[$i] = Storage::disk('public')->url($image);
                  }
                  $community['community'][$k]['many_imageUrl'] = $many_imageUrl;
                }
                $user = User::findOrFail($value['user_id']);
                $community['community'][$k]['user_name'] = $user->nickname;
                $community['community'][$k]['user_avatar'] = $user->avatar_url;
            }
        return $community;
    }

    //创建社区评论
    public function storeMessage(Request $request)
    {
        $user = $request->user();
        
    }
}
