<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MiniOpenidController extends Controller
{
//hyh小程序支付获取openid
    public function getOpenId(Request $request){
        $js_code = implode($request->js_code);
        $appid = env('MINIAPP_ID');
        $appsecret = env('MINIAPP_SECRET');

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".
            $appid."&secret=".$appsecret."&js_code=".$js_code."&grant_type=authorization_code";

        //通过code换取网页授权access_token
        $weixin=file_get_contents($url);
        //对JSON格式的字符串进行编码
        $jsondecode=json_decode($weixin);
        //转换成数组
        $array = get_object_vars($jsondecode);
        //输出openid
        $openid = $array['openid'];
        //返回给接口（小程序支付使用）
        return($openid);
    }
}
