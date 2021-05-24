<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'type', 'path', 'disk', 'size', 'size_kb',
    ];

    /* @array $appends */
    protected $appends = [
        'image_id', 'image_url',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getImageIdAttribute()
    {
        return $this->id;
    }

    public function getImageUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
