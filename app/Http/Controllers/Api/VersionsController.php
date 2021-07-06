<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Versions;
use Illuminate\Http\Request;

class VersionsController extends Controller
{
    //获取最新版本
    public function index()
    {
        $version = Versions::where('status','=',1)
            ->orderBy('id','desc')
            ->first();
        return $version;
    }

    //检测版本更新
    public function check(Request $request)
    {
        $request->validate([
            'version' => 'required|numeric', // 当前手机APP版本号
        ]);

        // 获取最新版本
        $version = Versions::where('status', '=', 1)
            ->orderBy('id', 'desc')
            ->first();

        $check = version_compare($request->version, $version->version, "<");

        if ($check) {
            $data['message'] = "有新版本";
            $data['version'] = $version->version;
            $data['url'] = $version->url;
            $data['description'] = $version->description;
            return response()->json($data, 200);
        } else {
            $data['message'] = "已经是最新版本";
            return response()->json($data, 403);
        }
    }
}
