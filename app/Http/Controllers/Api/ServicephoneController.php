<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServicephoneController extends Controller
{
//hyh新增客服电话
    public function phone()
    {
        $data['service_phone'] =config('website.phone');
        return response()->json($data, 200);
    }
}
