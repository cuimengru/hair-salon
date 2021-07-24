<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Versions extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    use SoftDeletes;

    // 平台 1-Android 2-iOS
    public const ANDROID = 1; // 审核中
    public const IOS = 2; // 已完成

    public static $platformMap = [
        self::ANDROID => 'Android',
        self::IOS => 'iOS',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'platform', 'version', 'description', 'url', 'status','ios_url','ios_version'
    ];
}
