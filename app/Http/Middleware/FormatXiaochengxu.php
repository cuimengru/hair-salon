<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FormatXiaochengxu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */

//hyh小程序筛选条件改造
    public function handle(Request $request, Closure $next)
    {

        $requestall = $request->all();

        file_put_contents("../1234567890-chushi.txt", var_export($requestall,true));
        foreach ($requestall as $key => $value) {

//            $value=json_decode($value,true);

            $needle = "filter_";//判断是否包含filter_这个字符
            if (strpos($key, $needle) !== false) { //如果$key中存在filter_这个字符串
//               echo 'true';
//               echo $key;
                $key_new = str_replace($needle, '', $key);
//              有filter_数据的时候，$filter_new 就不为空。
                $filter_new[$key_new] = $value;
            } else {
//              //没有filter_数据的时候：
                $zhanwei = "1";
            }
        }


        if (!empty($requestall['filter']) || !empty($filter_new)) {

//           如果原始数据中有filter数组：
            if (!empty($requestall['filter'])) {

//          4.既有filter数组，也有"filter_"数据：
                if (!empty($filter_new)) {
//                  最新的filter数组=新组合的+原来的还不是下划线形式的filter['age_id']
                    $requestall['filter'] = $filter_new + $requestall['filter'];
                }

//          3.有filter数组，没有"filter_"数据：
                if ($zhanwei == "1") {//没有filter_数据的时候：
                    $requestall['filter'] = $requestall['filter'];
                }

            } else {

//          2.没有filter数组，但有"filter_"数据：
                $requestall['filter'] = $filter_new;
            }
//
            $request->request->add(['filter' => $requestall['filter']]);


        } else {
//          1.没有filter数组 也没有"filter_"数据：
//            不用做任何处理

        }


//
//     file_put_contents("../1234567890-filter_new.txt", var_export($filter_new,true));
     file_put_contents("../1234567891-requestall.txt", var_export($requestall,true));
//     file_put_contents("../1234567892-request.txt", var_export($request,true));


//hyh小程序筛选条件改造 只针对当前接口可以是以下改法：
//        $shuzu=array(
//            'gender'=>$requestall['filter_gender'],
//            'age_id'=>$requestall['filter_age_id']
//        );
//
////        unset($requestall['filter_gender']);
////        unset($requestall['filter_age_id']);
//
//        $requestall['filter']=$shuzu+$requestall['filter'];
//
//
//        $request->request->add(['filter' => $requestall['filter']]);

//
//        file_put_contents("../123456789.txt", var_export($requestall,true));
////      file_put_contents("../1234567890.txt", var_export($a,true));
//        file_put_contents("../1234567891.txt", var_export($shuzu,true));

        return $next($request);
    }
}
