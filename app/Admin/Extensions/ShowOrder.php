<?php
/**
 *  显示用户的订单 限制 10条
 */

namespace App\Admin\Extensions;

use Illuminate\Contracts\Support\Renderable;
use App\Models\Order;
use App\Models\Product;
use Encore\Admin\Widgets\Table;

class ShowOrder implements Renderable
{
    public function render($key = null)
    {
        //$data = [];
        $order = Order::find($key);

        if ($order) {
//            foreach ($order as $k => $v) {
//                $product = Product::find($v['product_id']);
//                $product_name = $product ? $product->name : "";
//                $data[$k] = [$v['id'], $v['order_sn'], $v['number'], $v['created_at'], $v['paid_text'], $product_name];
//            }
            $html = '';
            if(!empty($order->ship_data['express_no'])){

            $express_no = $order->ship_data['express_no']; //物流单号
            $express_company = $order->ship_data['express_company']; //物流公司
            error_reporting(E_ALL || ~E_NOTICE);
            $host = "https://wdexpress.market.alicloudapi.com";
            $path = "/gxali";
            $method = "GET";
            $appcode = "8a7010f3210e423898353b477c243d8c";//开通服务后 买家中心-查看AppCode
            $headers = array();
            array_push($headers, "Authorization:APPCODE " . $appcode);
            $querys = "n=".$express_no."&t=".$express_company;

            $bodys = "";
            $url = $host . $path . "?" . $querys;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_FAILONERROR, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, true);

            if (1 == strpos("$" . $host, "https://")) {
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            }
            $out_put = curl_exec($curl);

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            list($header, $body) = explode("\r\n\r\n", $out_put, 2);
            //if ($httpCode == 200) {
                //print("正常请求计费(其他均不计费)<br>");
                //print($body);
                //return $body;
                //$data = [$body['']];
                /*foreach ($body['Traces'] as $k=>$v){
                    $data[$k] = [$v['AcceptStation'], $v['AcceptTime']];
                }*/
                $body_array = json_decode($body,ture);

               if($body_array['State'] == -1){
                    $body_array['State_text'] = "单号或快递公司代码错误";
               }elseif ($body_array['State'] == 0){
                   $body_array['State_text'] = "暂无轨迹";
               }elseif ($body_array['State'] == 1){
                   $body_array['State_text'] = "快递收件";
               }elseif ($body_array['State'] == 2){
                   $body_array['State_text'] = "在途中";
               }elseif ($body_array['State'] == 3){
                   $body_array['State_text'] = "签收";
               }elseif ($body_array['State'] == 4){
                   $body_array['State_text'] = "问题件";
               }elseif ($body_array['State'] == 5){
                   $body_array['State_text'] = "疑难件";
               }elseif ($body_array['State'] == 6){
                   $body_array['State_text'] = "退件签收";
               }
               $body_kuai[] = [$order->id,$body_array['LogisticCode'],$body_array['Name'],$body_array['Courier'],$body_array['CourierPhone'],$body_array['State_text']];
                //$data[] = [$body_array['Courier'], $body_array['Reason']];
                foreach ($body_array['Traces'] as $k=>$v){
                    $data[$k] = [$v['AcceptStation'], $v['AcceptTime']];
                }

                $html .= new Table(['Id','物流单号','快递公司','快递员或快递站','快递员电话','快递状态'],$body_kuai);
                $html .= new Table(['物流地点', '物流时间'], $data);
                return <<<HTML
{$html}
HTML;
            }else{
                $body_kuai[] = [$order->id,null,null,null,null,null];
                $data = [];
                $html .= new Table(['Id','物流单号','快递公司','快递员或快递站','快递员电话','快递状态'],$body_kuai);
                $html .= new Table(['物流地点', '物流时间'], $data);
                return <<<HTML
{$html}
HTML;
            }
            /*} else {
                if ($httpCode == 400 && strpos($header, "Invalid Param Location") !== false) {
                    print("参数错误");
                } elseif ($httpCode == 400 && strpos($header, "Invalid AppCode") !== false) {
                    print("AppCode错误");
                } elseif ($httpCode == 400 && strpos($header, "Invalid Url") !== false) {
                    print("请求的 Method、Path 或者环境错误");
                } elseif ($httpCode == 403 && strpos($header, "Unauthorized") !== false) {
                    print("服务未被授权（或URL和Path不正确）");
                } elseif ($httpCode == 403 && strpos($header, "Quota Exhausted") !== false) {
                    print("套餐包次数用完");
                } elseif ($httpCode == 500) {
                    print("API网关错误");
                } elseif ($httpCode == 0) {
                    print("URL错误");
                } else {
                    print("参数名错误 或 其他错误");
                    print($httpCode);
                    $headers = explode("\r\n", $header);
                    $headList = array();
                    foreach ($headers as $head) {
                        $value = explode(':', $head);
                        $headList[$value[0]] = $value[1];
                    }
                    print($headList['x-ca-error-message']);
                }
            }*/
        }

        //$html = new Table(['ID', '订单号', '购买数量', '购买时间', '状态', '产品名'], $data);
        /*return <<<HTML
{$html}
HTML;*/
    }
}
