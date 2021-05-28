<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\HelpCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HelpCenterController extends Controller
{
    //帮助中心列表
    public function index(Request $request)
    {
        $help = HelpCenter::orderBy('created_at', 'desc')->select('id','title')->paginate();
        return $help;
    }

    //某个帮助中心详情
    public function show($helpId, Request $request)
    {
        $help = HelpCenter::where('id','=',$helpId)->select('title','content')->first();
        return $help;
    }

    //提交反馈问题
    public function storefeedback(Request $request)
    {
        $image = image_ext(); // 上传图片类型
        $user = $request->user();

        $request->validate([
            'contents' => 'required|string',
            'many_images' => 'array',
        ]);

        if ($request->file('many_images')) {
            foreach ($request->file('many_images') as $k=>$value){
                $image = upload_images($value, 'feedback', $user->id);
                $attributes['many_images'][$k] = $image->path;
                //$avatar_image_id = array($image->id);
            }
        }
        //查询和清理多余头像
        /*foreach ($avatar_image_id as $a=>$item){
            if ($item > 0) {
                $avatars = DB::table('images')->where('id', '!=', $item)
                    ->where('type', '=', 'feedback')
                    ->where('user_id', '=', $user->id)
                    ->get();
                foreach ($avatars as $avatar) {
                    Storage::disk($avatar->disk)->delete($avatar->path);
                    DB::table('images')->where('id', '=', $avatar->id)->delete();
                }
            }
        }*/


        $feedback = Feedback::create([
            'user_id' => $user->id,
            'content' => $request->contents,
            'many_images' => $attributes['many_images'],
        ]);

        $data['message'] = "提交成功！";
        return response()->json($data, 200);
    }
}
