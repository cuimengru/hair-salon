<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionHair extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'name'
    ];
}
