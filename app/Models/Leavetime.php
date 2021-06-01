<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leavetime extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'designer_id', 'type', 'date', 'time'
    ];
    protected $casts = [
        'time' => 'json',
    ];

    public function getTimeAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setTimeAttribute($value)
    {
        $this->attributes['time'] = json_encode(array_values($value));
    }

    //关联设计师
    public function designer()
    {
        return $this->belongsTo(Designer::class);
    }

    //关联时间
    public function worktime()
    {
        return $this->hasMany(Worktime::class);
    }
}
