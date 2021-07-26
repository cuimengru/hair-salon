<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductionAge;
use App\Models\ProductionColor;
use App\Models\ProductionLength;
use App\Models\ProductionStyle;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    //作品年龄段
    public function agelist()
    {
        $age['age'] = ProductionAge::select('id','name','created_at')->get();
        $age['length'] = ProductionLength::select('id','name','created_at')->get();
        $age['color'] = ProductionColor::select('id','name','created_at')->get();
        $age['style'] = ProductionStyle::select('id','name','created_at')->get();
        return $age;
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
}
