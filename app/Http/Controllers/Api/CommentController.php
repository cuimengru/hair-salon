<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Designer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReserveOrder;
use App\Models\User;
use App\Services\SensitiveWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CommentController extends Controller
{
    //商品订单评价
    public function productStore(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rate' => 'required',
            'render_content' => 'required|string',
            'render_image' => 'array',
        ]);

        $order = Order::where('user_id','=',$user->id)
            ->where('id','=',$request->order_id)->first();
        $orderItem = OrderItem::where('user_id','=',$user->id)
        ->where('order_id','=',$request->order_id)->first();

        if (!$order->paid_at) {
            $data['message'] = "该订单未支付，不可评价";
            return response()->json($data, 403);
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            $data['message'] = "该订单已评价，不可重复提交";
            return response()->json($data, 403);
        }

        $bad_content = SensitiveWords::getBadWord($request->render_content);
        if(!empty($bad_content)){
            $attributes['render_content'] = SensitiveWords::replace($request->render_content,"***"); //替换敏感词为 ***
        }else{
            $attributes['render_content'] = $request->render_content;
        }

        //合成数组
        $many_images = array($request->file('image_0'),$request->file('image_1'),$request->file('image_2'),$request->file('image_3'),$request->file('image_4'),$request->file('image_5'),$request->file('image_6'),$request->file('image_7'),$request->file('image_8'),$request->file('image_9'));
        if (!empty($many_images)) {
            foreach ($many_images as $k => $value) {
                if ($value) {
                    $image = upload_images($value, 'comment', $user->id);
                    $attributes['render_image'][$k] = $image->path;
                } else {
                    $attributes['render_image'][$k] = null;
                }
            }
            $comment = Comment::create([
                'user_id' => $user->id,
                'order_id' => $request->order_id,
                'rate' => $request->rate,
                'render_content' => $attributes['render_content'],
                'render_image' => array_filter($attributes['render_image']),
                'product_id'=> $orderItem->product_id,
                'product_sku_id' => $orderItem->product_sku_id,
                'type' =>2,
            ]);
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
        }else{
            $comment = Comment::create([
                'user_id' => $user->id,
                'order_id' => $request->order_id,
                'rate' => $request->rate,
                'render_content' => $attributes['render_content'],
                //'render_image' => $attributes['render_image'],
                'product_id'=> $orderItem->product_id,
                'product_sku_id' => $orderItem->product_sku_id,
                'type' =>2,
            ]);
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
        }
        $data['message'] = "评价成功！";
        return response()->json($data, 200);
    }

    //预约订单评价
    public function reserveStore(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'reserveorder_id' => 'required|exists:reserve_orders,id',
            'rate' => 'required',
            'render_content' => 'required|string',
            'render_image' => 'array',
        ]);

        $order = ReserveOrder::where('user_id','=',$user->id)
            ->where('id','=',$request->reserveorder_id)->first();

        if (!$order->paid_at) {
            $data['message'] = "该订单未支付，不可评价";
            return response()->json($data, 403);
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            $data['message'] = "该订单已评价，不可重复提交";
            return response()->json($data, 403);
        }

        $bad_content = SensitiveWords::getBadWord($request->render_content);
        if(!empty($bad_content)){
            $attributes['render_content'] = SensitiveWords::replace($request->render_content,"***"); //替换敏感词为 ***
        }else{
            $attributes['render_content'] = $request->render_content;
        }
        //合成数组
        $many_images = array($request->file('image_0'),$request->file('image_1'),$request->file('image_2'),$request->file('image_3'),$request->file('image_4'),$request->file('image_5'),$request->file('image_6'),$request->file('image_7'),$request->file('image_8'),$request->file('image_9'));

        if (!empty($many_images)) {
            foreach ($many_images as $k => $value) {
                if ($value) {
                    $image = upload_images($value, 'comment', $user->id);
                    $attributes['render_image'][$k] = $image->path;
                } else {
                    $attributes['render_image'][$k] = null;
                }
            }
            $comment = Comment::create([
                'user_id' => $user->id,
                'reserveorder_id' => $request->reserveorder_id,
                'designer_id' => $order->designer_id,
                'rate' => $request->rate,
                'render_content' => $attributes['render_content'],
                'render_image' => array_filter($attributes['render_image']),
                'type' =>1,
            ]);
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
        }else{
            $comment = Comment::create([
                'user_id' => $user->id,
                'reserveorder_id' => $request->reserveorder_id,
                'designer_id' => $order->designer_id,
                'rate' => $request->rate,
                'render_content' => $attributes['render_content'],
                //'render_image' => $attributes['render_image'],
                'type' =>1,
            ]);
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
        }
        $data['message'] = "评价成功！";
        return response()->json($data, 200);
    }

    //某个产品的评价列表
    public function productIndex($productId, Request $request)
    {
        $products = QueryBuilder::for(Comment::class)
            /*->allowedFilters([
                AllowedFilter::exact('type'), //商品类型 1集品 2自营 3闲置
                'title'
            ])*/
            ->where('product_id','=',$productId)
            ->where('type','=',2)
            ->where('status','=',1)
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('user_id','product_id','product_sku_id','rate','render_content','render_image','render_video','created_at')
            ->paginate(8);

        foreach ($products as $t=>$item){
            $user = User::where('id','=',$item['user_id'])->first();
            $products[$t]['user_name'] = $user->nickname;
            $products[$t]['user_avatar'] = $user->avatar_url;
            if ($item['render_image']) {
                foreach ($item['render_image'] as $i => $image) {
                    $render_imageUrl[$i] = Storage::disk('oss')->url($image);
                }
                $products[$t]['render_imageUrl'] = $render_imageUrl;
            }
        }

        return $products;
    }

    //某个设计师的评价列表
    public function reserveIndex($designerId, Request $request)
    {
        $designers = QueryBuilder::for(Comment::class)
            /*->allowedFilters([
                AllowedFilter::exact('type'), //商品类型 1集品 2自营 3闲置
                'title'
            ])*/
            ->where('designer_id','=',$designerId)
            ->where('type','=',1)
            ->where('status','=',1)
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('id','user_id','rate','render_content','render_image','created_at')
            ->paginate(8);

        foreach ($designers as $t=>$item){
            $user = User::where('id','=',$item['user_id'])->first();
            $designers[$t]['user_name'] = $user->nickname;
            $designers[$t]['user_avatar'] = $user->avatar_url;
            if ($item['render_image']) {
                foreach ($item['render_image'] as $i => $image) {
                    $render_imageUrl[$i] = Storage::disk('oss')->url($image);
                }
                $designers[$t]['render_imageUrl'] = $render_imageUrl;
            }
        }

        return $designers;
    }
}
