<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Designer;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductLabel;
use App\Models\UserLikeDesigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserLikeController extends Controller
{
    //创建集品商品浏览记录
    public function likeProduct(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $record = UserLikeDesigner::whereUserId($user->id)->whereProductId($request->product_id)->first();
        if($record){
            $record->update([
                'count' => $record->count + 1,
            ]);
        }else{
            UserLikeDesigner::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'type' => 1,
            ]);
        }

        $data['message'] = "浏览成功！";
        return response()->json($data, 200);
    }

    //创建转售商品浏览记录
    public function likeIdleProduct(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'idleproduct_id' => 'required|exists:products,id',
        ]);
        $record = UserLikeDesigner::whereUserId($user->id)->whereidleproductId($request->idleproduct_id)->first();
        if($record){
            $record->update([
                'count' => $record->count + 1,
            ]);
        }else{
            UserLikeDesigner::create([
                'user_id' => $user->id,
                'idleproduct_id' => $request->idleproduct_id,
                'type' => 2,
            ]);
        }

        $data['message'] = "浏览成功！";
        return response()->json($data, 200);
    }

    //创建设计师浏览记录
    public function likeDesigner(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'designer_id' => 'required|exists:designers,id',
        ]);
        $record = UserLikeDesigner::whereUserId($user->id)->whereDesignerId($request->designer_id)->first();
        if($record){
            $record->update([
                'count' => $record->count + 1,
            ]);
        }else{
            UserLikeDesigner::create([
                'user_id' => $user->id,
                'designer_id' => $request->designer_id,
                'type' => 3,
            ]);
        }

        $data['message'] = "浏览成功！";
        return response()->json($data, 200);
    }

    //创建作品浏览记录
    public function likeProduction(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'production_id' => 'required|exists:productions,id',
        ]);
        $record = UserLikeDesigner::whereUserId($user->id)->whereProductionId($request->production_id)->first();
        if($record){
            $record->update([
                'count' => $record->count + 1,
            ]);
        }else{
            UserLikeDesigner::create([
                'user_id' => $user->id,
                'production_id' => $request->production_id,
                'type' => 4,
            ]);
        }

        $data['message'] = "浏览成功！";
        return response()->json($data, 200);
    }

    //浏览记录列表
    public function likeList(Request $request)
    {
        $user = $request->user();
        //商品记录
        if($request->filter['type'] == 1){
            $record = UserLikeDesigner::where('user_id','=',$user->id)
                ->where('type','=',1)
                ->orderBy('updated_at', 'desc')
                ->get();
            foreach ($record as $k=>$value){
                $record[$k] = Product::where('id','=',$value->product_id)
                    ->where('on_sale','=',1)
                    ->select('id','title','country_name','label_id','image','price','original_price','is_new','is_new_lable')
                    ->first();
                /*if($record[$k]['label_id']){
                    $record[$k]['label_name'] = ProductLabel::all()->whereIn('id',$record[$k]['label_id'])->pluck('name')->toArray();
                }*/
            }
            $record1 = json_decode(json_encode($record), true);
            $record2 = array_filter($record1);
            $count = count($record2); //总条数
            $page = $request->page;
            $pagesize = 8;
            $start=($page-1)*$pagesize;//偏移量，当前页-1乘以每页显示条数
            $product_orders1['data'] = array_slice($record2,$start,$pagesize);
            $product_orders1['total'] = $count;
            $product_orders1['current_page'] = $page;
            return $product_orders1;
        }

        //转售记录
        if($request->filter['type'] == 2){
            $record = UserLikeDesigner::where('user_id','=',$user->id)
                ->where('type','=',2)
                ->orderBy('updated_at', 'desc')
                ->get();
            foreach ($record as $k=>$value){
                $record[$k] = Product::where('id','=',$value->idleproduct_id)
                    ->where('on_sale','=',1)
                    ->select('id','title','country_name','label_id','image','price','original_price')
                    ->first();
                /*if($record[$k]['label_id']){
                    $record[$k]['label_name'] = ProductLabel::all()->whereIn('id',$record[$k]['label_id'])->pluck('name')->toArray();
                }else{
                    $record[$k]['label_name'] = null;
                }*/
            }

            $record1 = json_decode(json_encode($record), true);
            $record2 = array_filter($record1);
            $count = count($record2); //总条数
            $page = $request->page;
            $pagesize = 8;
            $start=($page-1)*$pagesize;//偏移量，当前页-1乘以每页显示条数
            $product_orders1['data'] = array_slice($record2,$start,$pagesize);
            $product_orders1['total'] = $count;
            $product_orders1['current_page'] = $page;
            return $product_orders1;
        }

        //发型师记录
        if($request->filter['type'] == 3){
            $record = UserLikeDesigner::where('user_id','=',$user->id)
                ->where('type','=',3)
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            foreach ($record as $k=>$value){
                $record[$k] = Designer::where('id','=',$value->designer_id)
                    ->select('id','name','position','thumb','label_id')
                    ->first();
            }

            return $record;
        }

        //作品记录
        if($request->filter['type'] == 4){
            $record = UserLikeDesigner::where('user_id','=',$user->id)
                ->where('type','=',4)
                ->orderBy('updated_at', 'desc')
                ->paginate(15);
            foreach ($record as $k=>$value){
                $record[$k] = Production::where('id','=',$value->production_id)
                    ->select('id','type','title','thumb','video')
                    ->first();
                $record[$k]['follows'] = DB::table('user_favorite_productions')
                    ->where('user_id','=',$user->id)
                    ->where('production_id','=',$value->production_id)
                    ->first();

                if ($record[$k]['follows']){
                    $record[$k]['follows_production'] = 1; //已收藏
                }else{
                    $record[$k]['follows_production'] = 0; //未收藏
                }
                unset($record[$k]['follows']);
            }

            return $record;
        }

    }
}
