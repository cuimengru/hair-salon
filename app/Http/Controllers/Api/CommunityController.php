<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\Community;
use App\Models\CommunityLike;
use App\Models\CommunityReview;
use App\Models\CommunityShield;
use App\Models\Report;
use App\Models\User;
use App\Services\MessageService;
use App\Services\SensitiveWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function MongoDB\BSON\toJSON;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Product;

class CommunityController extends Controller
{
    //发布社区内容
    public function store(Request $request)
    {
        $user = $request->user();
        //$video = video_ext();

        $request->validate([
            //'title' => 'required|string|min:4',
            'contents' => 'required|string',
            'many_images' => 'array',
            //'video' => 'string'.$video,
        ]);

        if(strlen($request->title) < 4){
            $data['message'] = "标题不能少于4个字符。";
            return response()->json($data, 403);
        }
        $bad_nickname = SensitiveWords::getBadWord($request->title);
        if(!empty($bad_nickname)){
            //$attributes['title'] = SensitiveWords::replace($request->title,"***"); //替换敏感词为 ***
            $data['message'] = " 存在敏感词，请重新输入。";
            return response()->json($data, 403);
        }else{
            $attributes['title'] = $request->title;
        }
        $bad_nickname1 = SensitiveWords::getBadWord($request->contents);
        if(!empty($bad_nickname1)){
            //$attributes['contents'] = SensitiveWords::replace($request->contents,"***"); //替换敏感词为 ***
            $data['message'] = " 存在敏感词，请重新输入。";
            return response()->json($data, 403);
        }else{
            $attributes['contents'] = $request->contents;
        }
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
                'title' => $attributes['title'],
                'content' => $attributes['contents'],
                'many_images' => array_filter($attributes['many_images']),
            ]);

        }
        if ($request->file('video')) {

            $video = get_vimeo_mp4($request->file('video'), 'community');

            $community = Community::create([
                'user_id' => $user->id,
                'title' => $attributes['title'],
                'content' => $attributes['contents'],
                'video' => $video,
            ]);
        }

        $data['message'] = "审核中！";
        return response()->json($data, 200);
        // $request->image-0;
    }

    //社区列表
    public function index(Request $request)
    {
        $community['banner'] = Advert::where('category_id', '=', 7)->orderBy('order', 'asc')->select('id','type','thumb','url','product_id')->get();
//      hyh如果广告链接的产品对此做是否存在和是否上架的判断  上方引入use App\Models\Product;
        foreach ($community['banner'] as $k=>$value){
            $product_sale=Product::where('id','=',$value['product_id'])->first();
            if($product_sale && $product_sale['on_sale']==1){
                $community['banner'][$k]['product_state']="1";
            }else{
                $community['banner'][$k]['product_state']="0";//不存在或已下架
            }
        }

        if($request->user_id){
            $shield = CommunityShield::where('user_id','=',$request->user_id)
                ->pluck('community_id')->toArray(); //拉黑
            $shield_user = Community::whereIn('id',$shield)->distinct()->pluck('user_id')->toArray();

            $community['community'] = QueryBuilder::for(Community::class)
                /*->allowedFilters([
                    AllowedFilter::exact('type') //商品类型 1集品 2自营 3闲置
                ])*/
                ->defaultSort('-created_at') //按照创建时间排序
                ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
                ->where('status', '=', 1)
                ->whereNotIn('user_id',$shield_user)
                ->select('id', 'user_id', 'title', 'content', 'many_images', 'video', 'created_at')
                ->paginate(3);

            foreach ($community['community'] as $k => $value) {
                if ($value['many_images']) {
                    foreach ($value['many_images'] as $i => $image) {
                        if($image){
                            $many_imageUrl[$k][$i] = Storage::disk('oss')->url($image);
                        }else{
                            $many_imageUrl[$k][$i] = null;
                        }

                    }
                    $community['community'][$k]['many_imageUrl'] = array_filter($many_imageUrl[$k]);
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
        }else{
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
                            $many_imageUrl[$k][$i] = Storage::disk('oss')->url($image);
                        }else{
                            $many_imageUrl[$k][$i] = null;
                        }

                    }
                    $community['community'][$k]['many_imageUrl'] = array_filter($many_imageUrl[$k]);
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
        $bad_nickname = SensitiveWords::getBadWord($request->message);
        if(!empty($bad_nickname)){
            //$attributes['message'] = SensitiveWords::replace($request->message,"***"); //替换敏感词为 ***
            $data['message'] = " 存在敏感词，请重新输入。";
            return response()->json($data, 403);
        }else{
            $attributes['message'] = $request->message;
        }
        $message = CommunityReview::create([
            'user_id' => $user->id,
            'replyuser_id' => $request->replyuser_id,
            'community_id' => $request->community_id,
            'message' => $attributes['message'],
        ]);
        $data['message'] = CommunityReview::where('community_id','=',$request->community_id)
            ->orderBy('created_at','desc')
            ->get();
        foreach ($data['message'] as $k=>$value){
            $user = User::where('id','=',$value['user_id'])->first();
            if($user){
                $data['message'][$k]['user_name'] = $user->nickname;
            }else{
                $data['message'][$k]['user_name'] = null;
            }

            if(!empty($value['replyuser_id'])){
                $replyuser = User::where('id','=',$value['replyuser_id'])->first();
                if($replyuser){
                    $data['message'][$k]['replyuser_name'] = $replyuser->nickname;
                }else{
                    $data['message'][$k]['replyuser_name'] = null;
                }

            }else{
                $data['message'][$k]['replyuser_name'] = null;
            }
        }
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

    //社区活动详情
    public function activeShow($activeId, Request $request)
    {
        $active = Advert::where('id','=',$activeId)->select('id','title','description','content','url','product_id')->first();
//      hyh如果广告链接的产品对此做是否存在和是否上架的判断
            $product_sale=Product::where('id','=',$active['product_id'])->first();
            if($product_sale && $product_sale['on_sale']==1){
                $active['product_state']="1";
            }else{
                $active['product_state']="0";//不存在或已下架
            }

        if(!$active){
            $data['message'] = "该广告位不存在！";
            return response()->json($data, 403);
        }
        return $active;
    }

    //创建社区举报功能
    public function report(Request $request)
    {
        $user = $request->user();

        $community = Community::where('id','=',$request->community_id)->first();
        if($community->user_id == $user->id){
            $data['message'] = "不能举报自己。";
            return response()->json($data, 403);
        }
        if(!$community){
            $data['message'] = "没有该社区。";
            return response()->json($data, 403);
        }

        $report = Report::where('user_id','=',$user->id)
            ->where('community_id','=',$request->community_id)
            ->first();

        if($report){
            $data['message'] = "已经举报过。";
            return response()->json($data, 403);
        }else{
            $reports = Report::create([
                'user_id' => $user->id,
                'community_id' => $request->community_id,
                'reason' => $request->reason,
            ]);

            $data['message'] = "举报成功！";
            return response()->json($data, 200);
        }

    }

    //创建社区拉黑功能
    public function shield(Request $request)
    {
        $user = $request->user();
        $community = Community::where('id','=',$request->community_id)->first();

        if($community->user_id == $user->id){
            $data['message'] = "不能拉黑自己。";
            return response()->json($data, 403);
        }
        if(!$community){
            $data['message'] = "没有该社区。";
            return response()->json($data, 403);
        }
        $shield = CommunityShield::where('user_id','=',$user->id)
            ->where('community_id','=',$request->community_id)
            ->first();

        if($shield){
            $data['message'] = "已经拉黑过。";
            return response()->json($data, 403);
        }else{
            $shields = CommunityShield::create([
                'user_id' => $user->id,
                'community_id' => $request->community_id,
            ]);

            $data['message'] = "拉黑成功！";
            return response()->json($data, 200);
        }

    }

    //删除自己发布的内容
    public function delete($Id, Request $request)
    {
       $user = $request->user();

        $community = Community::where('id','=',$Id)
            ->where('user_id','=',$user->id)->first();

        if($community){
            $community->delete();
            $data['message'] = "删除成功！";
            return response()->json($data, 200);
        }else{
            $data['message'] = "删除失败！";
            return response()->json($data, 403);
        }
       //return $user;
    }

}
