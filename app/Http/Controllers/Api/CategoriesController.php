<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\IdleCategory;
use App\Models\SelfCategory;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //集品类商品所有分类
    public function allcategory()
    {
        $tree = (new Category)->getCategoryTree();
        $tree = collect($tree)->toJson();
        $tree = json_decode($tree, true);
        $tree = array_values($tree);
        return response()->json($tree);
    }

    //自营类商品所有分类
    public function selfcategory()
    {
        $tree = (new SelfCategory)->getCategoryTree();
        $tree = collect($tree)->toJson();
        $tree = json_decode($tree, true);
        $tree = array_values($tree);
        return response()->json($tree);
    }

    //闲置类商品所有分类
    public function idlecategory()
    {
        $tree = (new IdleCategory)->getCategoryTree();
        $tree = collect($tree)->toJson();
        $tree = json_decode($tree, true);
        $tree = array_values($tree);
        return response()->json($tree);
    }
}
