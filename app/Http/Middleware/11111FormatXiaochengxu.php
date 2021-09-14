<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FormatXiaochengxu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */


    public function handle(Request $request, Closure $next)
    {

       $requestall=$request->all();
       foreach ($requestall as $key=>$value){
           $needle = "filter_";//判断是否包含filter_这个字符

           if (strpos($key, $needle) !== false) {
//               echo 'true';
//               echo $key;
               $key_new = str_replace($needle,'',$key);

               $filter[$key_new]=$value;
           }

       }
//        print_R($shuzu);
//        print_R($requestall['filter']);


        $request->request->add(['filter' => $filter]);

//        $requestall2=$request->all();
//
        file_put_contents("../123456789.txt", var_export($filter,true));
//        file_put_contents("../1234567890.txt", var_export($requestall2,true));
//        file_put_contents("../1234567891.txt", var_export($shuzu,true));





//只针对当前接口
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
