<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HelpCenterResource;
use App\Models\HelpCenter;
use Illuminate\Http\Request;

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
}
