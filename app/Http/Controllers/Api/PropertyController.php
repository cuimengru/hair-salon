<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductionAge;
use App\Models\ProductionColor;
use App\Models\ProductionLength;
use App\Models\ProductionStyle;
use App\Models\SensitiveWord;
use App\Services\SensitiveWords;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    //作品年龄段
    public function agelist()
    {
        $data = [];
        $age['age'] = ProductionAge::select('id','name')->get();
        $age['length'] = ProductionLength::select('id','name')->get();
        $age['color'] = ProductionColor::select('id','name')->get();
        $age['style'] = ProductionStyle::select('id','name','created_at')->get();
        $data[0] = ['name'=>'年龄','value'=>$age['age']];
        $data[1] = ['name'=>'长度','value'=>$age['length']];
        return $data;
    }

    public function lengthlist()
    {
        $length = ProductionLength::select('id','name','created_at')->get();
        return $length;
    }

    public function colorlist()
    {
        $color = ProductionColor::select('id','name','created_at')->get();
        return $color;
    }
    public function stylelist()
    {
        $style = ProductionStyle::select('id','name','created_at')->get();
        return $style;
    }

    //敏感词
    public function sensitiveWords(Request $request)
    {
        $words = SensitiveWord::where('word','=',$request->words)->first();
        if($words){
            $data['message'] = " 存在敏感词，请重新输入。";
            return response()->json($data, 403);
        }

        /*$words2 = $sensitiveWords->getInstance($request->words);
        if($words2){
            $data['message'] = " 存在敏感词，请重新输入。";
            return response()->json($data, 403);
        }*/
    }
}
