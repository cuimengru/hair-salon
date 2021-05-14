<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;

    protected $fillable = [
        'user_id','content'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
