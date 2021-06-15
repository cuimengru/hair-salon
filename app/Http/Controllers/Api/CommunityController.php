<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\Community;
use App\Models\CommunityLike;
use App\Models\CommunityReview;
use App\Models\User;
use App\Services\MessageService;
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

        //合成数组
        $many_images = array($request->file('image_0'),$request->file('image_1'),$request->file('image_2'),$request->file('image_3'),$request->file('image_4'),$request->file('image_5'),$request->file('image_6'),$request->file('image_7'),$request->file('image_8'),$request->file('image_9'));

        if (!empty($many_images)) {
            foreach ($many_images as $k=>$value){
                if($value){
                    $image = upload_images($value, 'community', $user->id);
                    $attributes['many_images'][$k] = $image->path;
                }else{
                    $attributes['many_images'][$k] = null;
                }

            }

            $community = Community::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'content' => $request->contents,
                'many_images' => array_filter($attributes['many_images']),
            ]);

        }
        if ($request->file('video')) {

            $video = get_vimeo_mp4($request->file('video'), 'community');

            $community = Community::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'content' => $request->contents,
                'video' => $video,
            ]);
        }

        $data['message'] = "发布成功！";
        return response()->json($data, 200);
        // $request->image-0;
    }

    //社区列表
    public function index(Request $request)
    {
        $community['banner'] = Advert::where('category_id', '=', 7)->orderBy('order', 'asc')->select('id', 'thumb', 'url')->get();
        $community['community'] = QueryBuilder::for(Community::class)
            /*->allowedFilters([
                AllowedFilter::exact('type') //商品类型 1集品 2自营 3闲置
            ])*/
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->where('status', '=', 1)
            ->select('id', 'user_id', 'title', 'content', 'many_images', 'video', 'created_at')
            ->paginate(3);
        foreach ($community['community'] as $k => $value) {
            if ($value['many_images']) {
                foreach ($value['many_images'] as $i => $image) {
                    if($image){
                        $many_imageUrl[$i] = Storage::disk('public')->url($image);
                    }else{
                        $many_imageUrl[$i] = null;
                    }

                }
                $community['community'][$k]['many_imageUrl'] = array_filter($many_imageUrl);
            }
            $user = User::findOrFail($value['user_id']);
            $community['community'][$k]['user_name'] = $user->nickname;
            $community['community'][$k]['user_avatar'] = $user->avatar_url;

            //评论内容
            $community['community'][$k]['reviews'] = CommunityReview::where('community_id','=',$value['id'])
                ->orderBy('created_at','desc')
                ->select('id','user_id','replyuser_id','message')
                ->get();
            foreach ($community['community'][$k]['reviews'] as $i=>$item){
                $user = User::where('id','=',$item['user_id'])->first();
                if($user){
                    $community['community'][$k]['reviews'][$i]['user_name'] = $user->nickname;
                }else{
                    $community['community'][$k]['reviews'][$i]['user_name'] = null;
                }

                if($item['replyuser_id']){
                    $replyuser = User::where('id','=',$item['replyuser_id'])->first();
                    if($replyuser){
                        $community['community'][$k]['reviews'][$i]['replyuser_name'] = $replyuser->nickname;
                    }else{
                        $community['community'][$k]['reviews'][$i]['replyuser_name'] = null;
                    }

                }else{
                    $community['community'][$k]['reviews'][$i]['replyuser_name'] = null;
                }
            }

            //评论数量
            $community['community'][$k]['reviews_number'] = CommunityReview::where('community_id','=',$value['id'])->count();
            //点赞数量
            $community['community'][$k]['like_number'] = CommunityLike::where('community_id','=',$value['id'])->count();

            //用户是否点赞
            if($request->user_id){
                $like = CommunityLike::where('community_id','=',$value['id'])
                    ->where('user_id','=',$request->user_id)->first();
                if($like){
                    $community['community'][$k]['user_like'] = 1; //已点赞
                }else{
                    $community['community'][$k]['user_like'] = 0; //未点赞
                }
            }else{
                $community['community'][$k]['user_like'] = 0; //未点赞
            }
        }



        return $community;
    }

    //创建社区评论
    public function storeMessage(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'community_id' => 'required|exists:communities,id', //社区id
            'message' => 'required|string', // 消息内容
            'replyuser_id' => 'int', //发送给某用户的id
        ]);
        $user = $request->user();
        //$replyuser_id = $request->replyuser_id;
        /*if($request->replyuser_id){
            //$replyuser_id = $request->replyuser_id;
            $check_user = CommunityReview::where('replyuser_id','=',$user->id)
                ->where('user_id','=',$request->replyuser_id)
                ->where('community_id','=',$request->community_id)->first();
            if($check_user){
                $user_id = $request->replyuser_id;
                $replyuser_id = $user->id;
                $type = 2; //回复
            }else{
                $user_id = $user->id;
                $replyuser_id = $request->replyuser_id;
                $type = 1;
            }
        }else{
            $user_id = $user->id;
            $replyuser_id = 0;
            $type = 1;
        }

        $community_id = $request->community_id;
        $msg['-1']['message'] = $request->message;
        $msg['-1']['type'] = $type;
        $msgService = app()->make(MessageService::class);
        $res = $msgService->storeMessage($user_id,$replyuser_id,$community_id,$msg);
        $data['messages'] = $res['messages'];
        $data['reviews_number'] = $res['reviews_number'];*/
        $message = CommunityReview::create([
            'user_id' => $user->id,
            'replyuser_id' => $request->replyuser_id,
            'community_id' => $request->community_id,
            'message' => $request->message,
        ]);
        $data['message'] = CommunityReview::where('community_id','=',$request->community_id)
            ->orderBy('created_at','desc')
            ->get();
        $data['reviews_number'] = CommunityReview::where('community_id','=',$request->community_id)->count();
        return response()->json($data, 200);
    }

    //创建社区评论点赞
    public function storelike(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'community_id' => 'required|exists:communities,id', //社区id
        ]);
        $res = CommunityLike::where('user_id','=',$user->id)
            ->where('community_id','=',$request->community_id)
            ->first();
        if($res){
            $like_number= CommunityLike::where('community_id','=',$request->community_id)->count(); //点赞数量
            $data['message'] = "已经点过赞！";
            $data['like_number'] = $like_number;
            return response()->json($data, 403);
        }else{
            $like = CommunityLike::create([
                'user_id' => $user->id,
                'community_id' => $request->community_id,
            ]);
        }


        $data['message'] = "点赞成功！";
        $like_number= CommunityLike::where('community_id','=',$request->community_id)->count(); //点赞数量
        $data['like_number'] = $like_number;
        return response()->json($data, 200);
    }

    //取消社区评论点赞
    public function deletelike(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'community_id' => 'required|exists:communities,id', //社区id
        ]);
        $res = CommunityLike::where('user_id','=',$user->id)
            ->where('community_id','=',$request->community_id)
            ->first();
        if($res){
            $res->delete();
            $data['message'] = "取消点赞成功！";
            $like_number= CommunityLike::where('community_id','=',$request->community_id)->count(); //点赞数量
            $data['like_number'] = $like_number;
            return response()->json($data, 200);
        }else{
            $data['message'] = "取消点赞失败！";
            $like_number= CommunityLike::where('community_id','=',$request->community_id)->count(); //点赞数量
            $data['like_number'] = $like_number;
            return response()->json($data, 403);
        }

    }
}
