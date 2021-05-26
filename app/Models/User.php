<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Encore\Admin\Traits\DefaultDatetimeFormat;
use Laravel\Passport\HasApiTokens;
use Overtrue\EasySms\PhoneNumber;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use DefaultDatetimeFormat;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar','nickname','phone','email_verified_at','remember_token','introduce',
        'integral','balance','status','created_at','updated_at','gender'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    /* @array $appends */
    protected $appends = [
        'avatar_url',
    ];
    // 返回头像链接
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::disk('public')->url($this->avatar);
        } else {
            return '';
        }
    }

    public function routeNotificationForEasySms($notification)
    {
        return new PhoneNumber($this->phone);
    }

    //收货地址
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    //购物车
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Passport 登录支持 邮箱 和 手机号码
     * @param $username
     * @return mixed
     */
    public function findForPassport($username)
    {
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        return self::where($credentials)->first();
    }

    //收藏设计师
    public function favoriteDesigners()
    {
        return $this->belongsToMany(Designer::class,'user_favorite_designers')
            ->withTimestamps()
            ->select('designers.id', 'designers.name', 'designers.thumb', 'designers.position', 'designers.description','designers.label_id')
            ->orderBy('user_favorite_designers.created_at', 'desc');
    }

    //收藏作品
    public function favoriteProductions()
    {
        return $this->belongsToMany(Production::class,'user_favorite_productions')
            ->withTimestamps()
            ->select('productions.id','productions.type','productions.title','productions.thumb')
            ->orderBy('user_favorite_productions.created_at','desc');
    }
}
