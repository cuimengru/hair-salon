<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use App\Models\Designer;
use App\Models\Fashion;
use App\Models\Production;
use App\Models\ProductionAge;
use App\Models\UserLikeDesigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Product;

class ProductionController extends Controller
{
    //作品首页
    public function index(Request $request)
    {
        //广告banner
        $index = [];
//        $index['advert'] = Advert::where('category_id','=',6)->orderBy('order', 'asc')->select('id','thumb', 'url')->get();hyh屏蔽
        $index['advert'] = Advert::where('category_id','=',6)->orderBy('order', 'asc')->select('id','type','thumb','url','product_id')->get();
//      hyh如果广告链接的产品对此做是否存在和是否上架的判断  上方引入 use App\Models\Product;
        foreach ($index['advert'] as $k=>$value){
            $product_sale=Product::where('id','=',$value['product_id'])->first();
            if($product_sale && $product_sale['on_sale']==1){
                $index['advert'][$k]['product_state']="1";
            }else{
                $index['advert'][$k]['product_state']="0";//不存在或已下架
            }
        }

        //作品$index['production']
        $productions= Production::where('is_recommend','=',1)
            ->where('on_sale','=',1)
            ->orderBy('sort','desc')//hyh推荐作品排序
            ->orderBy('created_at','desc')
            ->select('id','title','thumb','type','video','sort','is_new','is_newlable')
            ->get();
        foreach ($productions as $p=>$product){
            //收藏作品
            if($request->user_id){
                $productions[$p]['follows'] = DB::table('user_favorite_productions')
                    ->where('user_id','=',$request->user_id)
                    ->where('production_id','=',$product->id)
                    ->first();
                if ($productions[$p]['follows']){
                    $productions[$p]['follows_production'] = 1; //已收藏
                }else{
                    $productions[$p]['follows_production'] = 0; //未收藏
                }
                unset($productions[$p]['follows']);
            }else{
                $productions[$p]['follows_production'] = 0; //未收藏
            }
            $index1['production'] = $productions;

            if($productions[$p]['is_new']=="0"){
                $productions[$p]['is_newlable']="";
            }else{
                if($productions[$p]['is_newlable']==""){
                    $productions[$p]['is_newlable']="新品";
                }
            }
        }

//        hyhmodelname
        $index['production']['modelname'] = config('modelname.productions');
        $index['production']['list'] = $index1['production'];


        //设计师$index['designers']
        $index1['designers'] = Designer::where('is_recommend','=',1)
            ->where('is_employee','=',1)
            ->orderBy('sort','desc')//hyh推荐设计师排序
            ->orderBy('created_at','desc')
            ->select('id','name','thumb','description','position','label_id')->get();
        //收藏设计师
        foreach ($index1['designers'] as $d=>$designer){
            //收藏设计师
            if($request->user_id){
                $index1['designers'][$d]['follows'] = DB::table('user_favorite_designers')
                    ->where('user_id','=',$request->user_id)
                    ->where('designer_id','=',$designer->id)
                    ->first();
                if($index1['designers'][$d]['follows']){
                    $index1['designers'][$d]['follows_designer'] = 1; //已收藏
                }else{
                    $index1['designers'][$d]['follows_designer'] = 0; //未收藏
                }
                unset($index1['designers'][$d]['follows']);
            }else{
                $index1['designers'][$d]['follows_designer'] = 0; //未收藏
            }
        }
//        hyhmodelname
        $index['designers']['modelname'] = config('modelname.designers');
        $index['designers']['list'] = $index1['designers'];


////   资讯 hyh挪动到app首页去了
//        $index1['fashions'] = Fashion::where('is_recommend','=',1)
//            ->orderBy('order','asc')
//            ->orderBy('created_at','desc')
//            ->select('id','title','thumb','description','created_at','updated_at')
//            ->paginate(4);
//        foreach ($index1['fashions'] as $k=>$value){
//            $index1['fashions'][$k]['created_time'] = date("Y.m.d", strtotime($value['created_at']));
//            $index1['fashions'][$k]['updated_time'] = date("Y.m.d", strtotime($value['updated_at']));
//        }
//
////        hyhmodelname
//        $index['fashions']['modelname'] = config('modelname.fashions');
//        $index['fashions']['list'] = $index1['fashions'];

        return $index;
    }

    //作品详情
    public function show($Id, Request $request)
    {
        $production = Production::where('id','=',$Id)
            ->select('title','description','content','thumb','video','many_images')
            ->first();
        if($production['many_images']){
            foreach ($production['many_images'] as $k=>$value){
                $many_imageUrl[$k] = Storage::disk('oss')->url($value);
            }
            $production['many_imageUrl'] = $many_imageUrl;
        }
        if($request->user_id){
            $production['follows'] = DB::table('user_favorite_productions')
                ->where('user_id','=',$request->user_id)
                ->where('production_id','=',$Id)
                ->first();
            if ($production['follows']){
                $production['follows_production'] = 1; //已收藏
            }else{
                $production['follows_production'] = 0; //未收藏
            }
            unset($production['follows']);
            $record = UserLikeDesigner::whereUserId($request->user_id)->whereProductionId($Id)->first();
            if($record){
                $record->update([
                    'count' => $record->count + 1,
                ]);
            }else{
                UserLikeDesigner::create([
                    'user_id' => $request->user_id,
                    'production_id' => $Id,
                    'type' => 4,
                ]);
            }


        }else{
            $production['follows_production'] = 0; //未收藏
        }
        //浏览次数
        $record = UserLikeDesigner::where('production_id','=',$Id)->count();
        $production_total = Production::where('id','=',$Id)->first();
        $production_total->update([
            'rating' => $record,
        ]);

        return $production;
    }

    //收藏作品
    public function favor(Production $production,Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProductions()->find($production->id)) {
            $data['message'] = " 已经收藏！";
            return response()->json($data, 403);
        }
        $user->favoriteProductions()->attach($production);

        $data['message'] = "收藏成功！";
        return response()->json($data, 200);
    }

    //取消收藏作品
    public function disfavor(Production $production,Request $request)
    {
        $user = $request->user();
        $user->favoriteProductions()->detach($production);

        $data['message'] = "取消成功！";
        return response()->json($data, 200);
    }

    //收藏作品列表
    public function followlist(Request $request)
    {
        $production = $request->user()->favoriteProductions()->paginate(9);
        foreach ($production as $k=>$value){
            unset($production[$k]['many_images']);
            //unset($production[$k]['video']);
            unset($production[$k]['description']);
            unset($production[$k]['content']);
            unset($production[$k]['rating']);
            unset($production[$k]['is_recommend']);
            unset($production[$k]['created_at']);
            unset($production[$k]['updated_at']);
            unset($production[$k]['pivot']);

            if($production[$k]['is_new']=="0"){
                $production[$k]['is_newlable']="";
            }else{
                if($production[$k]['is_newlable']==""){
                    $production[$k]['is_newlable']="新品";
                }
            }
        }
        return $production;
    }

    //全部作品列表
    public function allIndex(Request $request)
    {
//        $filter_gender='gender';
//        print_R($gender);
//        exit();
//

        $hhh = $request->all();
        file_put_contents("../1234-kongzhiqi.txt", var_export($hhh,true));

        $productions = QueryBuilder::for(Production::class)
            ->allowedFilters([
                'gender', //性别
                //AllowedFilter::exact('type'), //作品类型 hyh新增作品类型筛选
                'type',
                'style_id',
                'age_id',
                'hair_id',
                'height_id',//hyh身高改多选
                'color_id',
                'length_id',
                'face_id',
                'project_id',
            ])
            ->where('on_sale','=',1)
            ->where('is_recommend','=',0)//hyh客户要求，列表页不显示推荐的作品。
            ->defaultSort('-sort_list') //hyh作品排序
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('id','title','thumb','type','video','sort_list','is_new','is_newlable')
            ->paginate(15);


//            用like方法，要先根据逗号拆解 过滤器返回的多个值，然后逐个值进行like。
//            ？？？？？不拆值的通俗mysql查询结果
//                select * from productions where age_id like '%5%' and age_id like '%4%'

        foreach ($productions as $p=>$product){
            //收藏作品
            if($request->user_id){
                $productions[$p]['follows'] = DB::table('user_favorite_productions')
                    ->where('user_id','=',$request->user_id)
                    ->where('production_id','=',$product->id)
                    ->first();
                if ($productions[$p]['follows']){
                    $productions[$p]['follows_production'] = 1; //已收藏
                }else{
                    $productions[$p]['follows_production'] = 0; //未收藏
                }
                unset($productions[$p]['follows']);
            }else{
                $productions[$p]['follows_production'] = 0; //未收藏
            }
            $index['production'] = $productions;


            if($productions[$p]['is_new']=="0"){
                $productions[$p]['is_newlable']="";
            }else{
                if($productions[$p]['is_newlable']==""){
                    $productions[$p]['is_newlable']="新品";
                }
            }

        }


        return $productions;
    }

    //全部作品列表_小程序
    public function allIndex_xiaochengxu(Request $request)
    {
//        $filter_gender='gender';
//        print_R($gender);
//        exit();
//

        $requestall = $request->all();
//        file_put_contents("../9999999999999-111.txt", var_export($requestall,true));

//     $requestall['filter']= array (
//     'gender' => NULL,
//     'height_id' => NULL,
//     'age_id' => NULL,
//     'color_id' => NULL,
//     'length_id' => NULL,
//     'face_id' => NULL,
//     'style_id' => NULL,
//     'project_id' => NULL,
//     'hair_id' => NULL,
//     'type' => 0,
//   );


//   file_put_contents("../9999999999999-333333333333.txt", var_export($requestall['filter'],true));

        if(isset($requestall['filter'])){

            $type=$requestall['filter']['type'];
            if(!empty($type)){
                if($type!="\"\""){//选择“不限”
                    $type= explode(",",$type);
                }else{
                    $type=NULL;
                }
            }

            $gender=$requestall['filter']['gender'];
            if(!empty($gender)){
                if($gender!="\"\""){
                    $gender= explode(",",$gender);
                }else{
                    $gender=NULL;
                }
            }

            $age_id=$requestall['filter']['age_id'];
            if(!empty($age_id)){
                if($age_id!="\"\""){
                    $age_id_new='%'.$age_id.'%';
                    $age_id=str_replace(',','%,%',$age_id_new);
                }else{
                    $age_id='%[%';
                }
            }

            $style_id=$requestall['filter']['style_id'];
            if(!empty($style_id)){
                if($style_id!="\"\""){
                    $style_id_new='%'.$style_id.'%';
                    $style_id=str_replace(',','%,%',$style_id_new);
                }else{
                    $style_id='%[%';
                }
            }

            $hair_id=$requestall['filter']['hair_id'];
            if(!empty($hair_id)){
                if($hair_id!="\"\""){
                    $hair_id_new='%'.$hair_id.'%';
                    $hair_id=str_replace(',','%,%',$hair_id_new);
                }else{
                    $hair_id='%[%';
                }
            }

            $height_id=$requestall['filter']['height_id'];
            if(!empty($height_id)){
                if($height_id!="\"\""){
                    $height_id_new='%'.$height_id.'%';
                    $height_id=str_replace(',','%,%',$height_id_new);
                }else{
                    $height_id='%[%';
                }
            }

            $color_id=$requestall['filter']['color_id'];
            if(!empty($color_id)){
                if($color_id!="\"\""){
                    $color_id_new='%'.$color_id.'%';
                    $color_id=str_replace(',','%,%',$color_id_new);
                }else{
                    $color_id='%[%';
                }
            }

            $length_id=$requestall['filter']['length_id'];
            if(!empty($length_id)){
                if($length_id!="\"\""){
                    $length_id_new='%'.$length_id.'%';
                    $length_id=str_replace(',','%,%',$length_id_new);
                }else{
                    $length_id='%[%';
                }
            }

            $face_id=$requestall['filter']['face_id'];
            if(!empty($face_id)){
                if($face_id!="\"\""){
                    $face_id_new='%'.$face_id.'%';
                    $face_id=str_replace(',','%,%',$face_id_new);
                }else{
                    $face_id='%[%';
                }
            }

            $project_id=$requestall['filter']['project_id'];
            if(!empty($project_id)){
                if($project_id!="\"\""){
                    $project_id_new='%'.$project_id.'%';
                    $project_id=str_replace(',','%,%',$project_id_new);
                }else{
                    $project_id='%[%';
                }
            }

        }else{
            $type=NULL;
            $gender=NULL;
            $age_id='%[%';
            $style_id='%[%';
            $hair_id='%[%';
            $height_id='%[%';
            $color_id='%[%';
            $length_id='%[%';
            $face_id='%[%';
            $project_id='%[%';
        }

//  $type=NULL;

// $aaagggeee="%1%,%2%";
// $aaagggeee='%[%';


        // $request->request->add(['filter' => $requestall['filter']]);

//        file_put_contents("../9999999999999-222.txt", var_export($gender,true));

        $productions = QueryBuilder::for(Production::class)
            ->allowedFilters([
                // 'gender', //性别
                AllowedFilter::exact('gender')->default($gender),
                AllowedFilter::exact('type')->default($type), //作品类型 hyh新增作品类型筛选
                // 'type',
                // 'style_id',
                // 'age_id',
                // AllowedFilter::exact('age_id')->default($age_id),
                // 'hair_id',
                // 'height_id',//hyh身高改多选
                // 'color_id',
                // 'length_id',
                // 'face_id',
                // 'project_id',
            ])
            ->where('age_id','like',$age_id)
            ->where('style_id','like',$style_id)
            ->where('hair_id','like',$hair_id)
            ->where('height_id','like',$height_id)
            ->where('color_id','like',$color_id)
            ->where('length_id','like',$length_id)
            ->where('face_id','like',$face_id)
            ->where('project_id','like',$project_id)
            ->where('on_sale','=',1)
            ->where('is_recommend','=',0)//hyh客户要求，列表页不显示推荐的作品。
            ->defaultSort('-sort_list') //hyh作品排序
            ->defaultSort('-created_at') //按照创建时间排序
            ->allowedSorts('updated_at') // 支持排序字段 更新时间 价格
            ->select('id','title','thumb','type','video','sort_list','is_new','is_newlable')
            ->paginate(15);


//            用like方法，要先根据逗号拆解 过滤器返回的多个值，然后逐个值进行like。
//            ？？？？？不拆值的通俗mysql查询结果
//                select * from productions where age_id like '%5%' and age_id like '%4%'

        foreach ($productions as $p=>$product){
            //收藏作品
            if($request->user_id){
                $productions[$p]['follows'] = DB::table('user_favorite_productions')
                    ->where('user_id','=',$request->user_id)
                    ->where('production_id','=',$product->id)
                    ->first();
                if ($productions[$p]['follows']){
                    $productions[$p]['follows_production'] = 1; //已收藏
                }else{
                    $productions[$p]['follows_production'] = 0; //未收藏
                }
                unset($productions[$p]['follows']);
            }else{
                $productions[$p]['follows_production'] = 0; //未收藏
            }
            $index['production'] = $productions;


            if($productions[$p]['is_new']=="0"){
                $productions[$p]['is_newlable']="";
            }else{
                if($productions[$p]['is_newlable']==""){
                    $productions[$p]['is_newlable']="新品";
                }
            }

        }


        return $productions;
    }

}
