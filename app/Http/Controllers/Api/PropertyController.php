<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductionAge;
use App\Models\ProductionColor;
use App\Models\ProductionFace;
use App\Models\ProductionHair;
use App\Models\ProductionHeight;
use App\Models\ProductionLength;
use App\Models\ProductionProject;
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
        $age['gender'] = [['id'=>0,'name'=>'男'],['id'=>1,'name'=>'女']]; //性别
        $age['height'] = ProductionHeight::select('id','name')->get(); //身高
        $age['age'] = ProductionAge::select('id','name')->get(); //年龄
        $age['color'] = ProductionColor::select('id','name')->get(); //作品发质
        $age['length'] = ProductionLength::select('id','name')->get(); //长度
        $age['face'] = ProductionFace::select('id','name')->get(); //脸型
        $age['style'] = ProductionStyle::select('id','name')->get(); //风格
        $age['project'] = ProductionProject::select('id','name')->get(); //项目
        $age['hair'] = ProductionHair::select('id','name')->get(); //烫染
        $age['type'] = [['id'=>0,'name'=>'视频'],['id'=>1,'name'=>'图文']]; //作品类型

//        hyh小程序筛选条件改造
//        $data[0] = ['name'=>'性别','key'=>'filter[gender]','value'=>$age['gender']];
        $data[0] = ['name'=>'性别','key'=>'filter_gender','value'=>$age['gender']];
        $data[1] = ['name'=>'身高','key'=>'filter_height_id','value'=>$age['height']];
        $data[2] = ['name'=>'年龄','key'=>'filter_age_id','value'=>$age['age']];
        $data[3] = ['name'=>'发质','key'=>'filter_color_id','value'=>$age['color']];
        $data[4] = ['name'=>'长度','key'=>'filter_length_id','value'=>$age['length']];
        $data[5] = ['name'=>'脸型','key'=>'filter_face_id','value'=>$age['face']];
        $data[6] = ['name'=>'风格','key'=>'filter_style_id','value'=>$age['style']];
        $data[7] = ['name'=>'项目','key'=>'filter_project_id','value'=>$age['project']];
        $data[8] = ['name'=>'烫染','key'=>'filter_hair_id','value'=>$age['hair']];
        $data[9] = ['name'=>'作品类型','key'=>'filter_type','value'=>$age['type']];
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
