<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IosswitchController extends Controller
{
//hyh新增苹果审核开关
    public function iosswitch()
    {
        $data['ios_switch'] =config('website.ios_switch');
        return response()->json($data, 200);
    }
}
