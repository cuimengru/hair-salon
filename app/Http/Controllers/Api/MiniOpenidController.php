<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MiniOpenidController extends Controller
{
//hyh小程序支付获取openid
    public function getOpenId(Request $request){
        $js_code = $request->js_code;
        $appid = env('MINIAPP_ID');
        $appsecret = env('MINIAPP_SECRET');
        $lll=env('WECHAT_PAY_KEY');

        file_put_contents("../hyh-appid.txt", var_export($lll,true));
        file_put_contents("../hyh-appsecret.txt", var_export($appsecret,true));
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".
            $appid."&secret=".$appsecret."&js_code=".$js_code."&grant_type=authorization_code";

//        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".
//            $appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";


//       https://api.weixin.qq.com/sns/jscode2session?appid=wx3f8c2c0a44cf1a50&secret=a82040dcb8c464b2b2e5b6444f159f5a&js_code=011EynFa1PuLXB0D6mGa1AEDD84EynF8&grant_type=authorization_code

        file_put_contents("../hyh-url.txt", var_export($url,true));
        //通过code换取网页授权access_token
        $weixin=file_get_contents($url);
        //对JSON格式的字符串进行编码
        $jsondecode=json_decode($weixin);
        //转换成数组
        $array = get_object_vars($jsondecode);
        file_put_contents("../hyh-array.txt", var_export($array,true));

        //输出openid

        //返回给接口（小程序支付使用）


        if(!empty($array['openid'])){

            $data['mini_openid'] =$array['openid'];
            return response()->json($data, 200);

        }else{

            return response()->json($array, 205);

        }



    }
}