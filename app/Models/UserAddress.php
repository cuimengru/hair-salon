<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;
    use DefaultDatetimeFormat;
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'street',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
        'status',
    ];
    protected $dates = ['last_used_at'];

    /* @array $appends */
    protected $appends = [
        'full_address',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        if($this->street){
            return "{$this->province}{$this->city}{$this->district}{$this->street}{$this->address}";
        }else{
            return "{$this->province}{$this->city}{$this->district}{$this->address}";
        }

    }
}
