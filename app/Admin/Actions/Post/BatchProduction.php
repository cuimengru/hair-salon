<?php

namespace App\Admin\Actions\Post;

use App\Models\ProductionAge;
use App\Models\ProductionColor;
use App\Models\ProductionFace;
use App\Models\ProductionHair;
use App\Models\ProductionHeight;
use App\Models\ProductionLength;
use App\Models\ProductionProject;
use App\Models\ProductionStyle;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BatchProduction extends BatchAction
{
    public $name = '批量上传作品属性';
    protected $selector = '.production-shuxing';

    public function handle(Collection $collection,Request $request)
    {
        // 获取到表单中的`issue`值
        $request->get('c');
        $request->get('age_id');
        $request->get('length_id');
        $request->get('color_id');
        $request->get('style_id');
        $request->get('height_id');
        $request->get('face_id');
        $request->get('project_id');
        $request->get('hair_id');

        foreach ($collection as $model) {
            $model->update([
                'gender'=> $request->get('gender'),
                'age_id'=> $request->get('age_id'),
                'length_id'=> $request->get('length_id'),
                'color_id'=> $request->get('color_id'),
                'style_id'=> $request->get('style_id'),
                'height_id'=> $request->get('height_id'),
                'face_id'=> $request->get('face_id'),
                'project_id'=> $request->get('project_id'),
                'hair_id'=> $request->get('hair_id'),
            ]);
        }

        return $this->response()->success('上传成功')->refresh();
    }

    public function form()
    {

        $this->select('gender','性别')->options([
            '0'=>'男',
            '1'=> '女'
        ])->default(0);
        $this->select('height_id','身高')->options(ProductionHeight::all()->pluck('name','id'));
        $this->multipleSelect('age_id','年龄段')->options(ProductionAge::all()->pluck('name','id'));
        $this->select('color_id','发质')->options(ProductionColor::all()->pluck('name','id'));
        $this->select('length_id','长度')->options(ProductionLength::all()->pluck('name','id'));
        $this->select('face_id','脸型')->options(ProductionFace::all()->pluck('name','id'));
        $this->multipleSelect('style_id','风格')->options(ProductionStyle::all()->pluck('name','id'));
        $this->select('project_id','项目')->options(ProductionProject::all()->pluck('name','id'));
        $this->multipleSelect('hair_id','烫染')->options(ProductionHair::all()->pluck('name','id'));
    }

    public function html()
    {
        return "<a class='production-shuxing btn btn-sm btn-twitter'> <i class='fa fa-info-circle'></i> 批量上传作品属性</a>";
    }

}
