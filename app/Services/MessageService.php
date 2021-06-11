<?php

namespace App\Services;

use App\Models\CommunityReview;
use App\Models\User;


class MessageService
{
    /**
     * @param int $user_id 用户ID
     * @param int $replyuser_id 回复用户ID
     * @param int $community_id 评论ID
     * @param array $msg 消息内容
     * @param int $type 类型 1-用户 2-回复用户
     * @return array
     */
    public function storeMessage(int $user_id, int $replyuser_id,int $community_id, array $msg)
    {
        //查询是否有评论符合user_id 和 replyuser_id
        $check = CommunityReview::where('user_id',$user_id)
            ->where('community_id',$community_id)
            ->where('replyuser_id',$replyuser_id)
            ->first();

        if($check){
            //有的话 附加新留言到原来的留言后面
            $new_message = $check->message + $msg;
            $check->update([
                'message' => $new_message,
            ]);
        }else{
            //没有 新创建评论
            $message = CommunityReview::create([
                'user_id' => $user_id,
                'replyuser_id' => $replyuser_id,
                'community_id' => $community_id,
                'message' => $msg,
            ]);
        }

        //$res['message'] = "评论成功！";
        //$res['status'] = 200;
        $res['messages'] = CommunityReview::where('community_id',$community_id)
            ->orderBy('updated_at','desc')
            ->get();
        foreach ($res['messages'] as $k=>$value){
            $user = User::where('id','=',$value['user_id'])->first();
            if($user){
                $res['messages'][$k]['user_name'] = $user->nickname;
            }else{
                $res['messages'][$k]['user_name'] = null;
            }

            if(!empty($value['replyuser_id'])){
                $replyuser = User::where('id','=',$value['replyuser_id'])->first();
                if($replyuser){
                    $res['messages'][$k]['replyuser_name'] = $replyuser->nickname;
                }else{
                    $res['messages'][$k]['replyuser_name'] = null;
                }

            }else{
                $res['messages'][$k]['replyuser_name'] = null;
            }
            $res['messages'][$k]['reviews_total'] = count($value['message']);
        }
        //评论数量
        $reviews = $res['messages']->toArray();
        $res['reviews_number'] = array_sum(array_column($reviews,'reviews_total'));

        $res['status'] = 200;
        //$res['message'] = array_values($reviews);
        return $res;

    }
}
