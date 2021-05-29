<?php

namespace App\Services;

use App\Models\CommunityReview;


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
            ->where('replyuser_id',$replyuser_id)
            ->where('community_id',$community_id)
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
                ''
            ]);
        }

    }
}
