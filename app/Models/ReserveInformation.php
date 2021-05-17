<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReserveInformation extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'designer_id','service_project','time'
    ];

    public function designer()
    {
        return $this->belongsTo(Designer::class);
    }
    public function service()
    {
        return $this->hasMany(ServiceProject::class);
    }
}
