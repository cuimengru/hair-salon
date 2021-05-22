<?php

namespace App\Models;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdleCategory extends Model
{
    use HasFactory;
    use ModelTree, AdminBuilder;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTitleColumn('name');// title 字段
    }
    protected $fillable = ['name','parent_id','is_directory', 'level', 'path','order'];
    protected $casts = [
        'is_directory' => 'boolean',
    ];

    protected $hidden = [
        'is_directory','path'
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听 Category 的创建事件，用于初始化 path 和 level 字段值
        static::updating(function ($modal) {
            if (is_null($modal->parent_id) || empty($modal->parent_id) || $modal->parent_id == 0) {// 如果创建的是一个根类目
                $modal->level = 1;// 层级设置为0
                // 将 path 设为 -
                $modal->path  = '-';
                return true;
            } else {
                $modal->level = $modal->parent->level + 1; // 将层级设为父类目层级 + 1
                // 将 path 值设为父类目的 path 追加父类目 ID 以及最后跟上一个 - 分隔符
                $modal->path  = $modal->parent->path.$modal->parent_id.'-';
                return true;
            }
        }); // 修改记录的时候触发
    }
    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    // 定义一个访问器，获取所有祖先类目的 ID 值
    public function getPathIdsAttribute()
    {
        // trim($str, '-') 将字符串两端的 - 符号去除
        // explode() 将字符串以 - 为分隔切割为数组
        // 最后 array_filter 将数组中的空值移除
        return array_filter(explode('-', trim($this->path, '-')));
    }

    // 定义一个访问器，获取所有祖先类目并按层级排序
    public function getAncestorsAttribute()
    {
        return Category::query()
            // 使用上面的访问器获取所有祖先类目 ID
            ->whereIn('id', $this->path_ids)
            // 按层级排序
            ->orderBy('level')
            ->get();
    }

    // 定义一个访问器，获取以 - 为分隔的所有祖先类目名称以及当前类目的名称
    public function getFullNameAttribute()
    {
        return $this->ancestors  // 获取所有祖先类目
        ->pluck('name') // 取出所有祖先类目的 name 字段作为一个数组
        ->push($this->name) // 将当前类目的 name 字段值加到数组的末尾
        ->implode(' - '); // 用 - 符号将数组的值组装成一个字符串
    }

    /**
     * 递归获取分类目录树
     * @param null $parentId
     * @param null $allCategories
     * @return Category[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getCategoryTree($parentId = null, $allCategories = null)
    {
        if (is_null($allCategories)) {
            $allCategories = Category::all();// 取出所有类目
        }

        return $allCategories
            ->where('parent_id', $parentId)
            ->sortBy('order')
            // 遍历分类，并用返回值构建一个新的集合
            ->map(function (Category $category) use ($allCategories) {
                $data = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'order' => $category->order,
                    'level' => $category->level,
                ];
                // 如果存在子类目，进行递归调用，并将子类目的数据存入 children 字段中
                $data['children'] = $this->getCategoryTree($category->id, $allCategories)->toArray();
                $data['children'] = array_values($data['children']);
                if (empty($data['children'])) {
                    unset($data['children']);
                }
                return $data;
            });
    }
}
