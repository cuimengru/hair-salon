<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Notifications\VerificationCode;
use App\Services\SensitiveWords;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Leonis\Notifications\EasySms\Channels\EasySmsChannel;
use Overtrue\EasySms\PhoneNumber;
use App\Notifications\EmailVerify;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    /**
     * 发送验证码短信
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sms(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|numeric',
        ]);

        $phone = $request->phone;

        $user = User::where('phone','=',$phone)->first();

        $code = str_pad(random_int(1, 999999), 6, 0, STR_PAD_LEFT); // 生成6位随机数，左侧补0
        /*if($user){
            $user->notify(new VerificationCode($code));
            Notification::send($user,new VerificationCode($code));
        }*/
        Notification::route(
            EasySmsChannel::class,
            new PhoneNumber($phone)
        )->notify(new VerificationCode($code)); //发送短信验证码
        $key = 'verificationCode_' . $phone;
        $expiredAt = now()->addMinutes(5);
        $verifyData = \Cache::get($key);
        /*if ($verifyData) {
            abort(403,'已经发送过验证码了');
        }*/

        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        $data['message'] = "验证码发送成功";
        $data['key'] = $key;
        //$data['code'] = $code;
        //$data['expired_at'] = $expiredAt->toDateTimeString();
        return response()->json($data, 200);
    }

    //用户注册
    public function store(UserRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            abort(403, '验证码已失效');
        }

        if(!hash_equals($verifyData['code'], $request->verification_code)){
            // 返回401
            throw new AuthenticationException('验证码错误');
        }
        $user1 = User::where('phone','=',$verifyData['phone'])->first();
        if($user1){
            $data['message'] = '用户已注册过！';
            return response()->json($data, 403);
        }
        $user = User::create([
            'phone' => $verifyData['phone'],
            'password' => bcrypt($request->password),
            'nickname' => substr_replace($verifyData['phone'],'****',3,4),
            'type' => 0,
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return new UserResource($user);
    }

    /**
     * 当前登录用户详细信息
     * @param Request $request
     * @return User|mixed
     */
    public function me(Request $request)
    {
        return $request->user();
    }

    /**
     * 我的 个人中心
     * @param Request $request
     * @return array
     */
    public function my(Request $request)
    {
        $user = $request->user();

        $data = [
            'id' => $user->id,
            'phone' => $user->phone, // 手机号码
            'nickname' => $user->nickname, // 昵称
            'name' => $user->name, // 账户
            'avatar' => $user->avatar, //头像
            'gender' => $user->gender,//性别
            //'introduce' => $user->introduce, //简介
            'balance' => $user->balance, //余额
            'integral' => $user->integral,
            'status' => $user->status, //审核状态:0未审核1已审核-1审核中
            'avatar_url' => $user->avatar_url,
        ];

        return $data;
    }

    // 上传和修改头像
    public function avatar(Request $request)
    {
        $image = image_ext(); // 上传图片类型
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|mimes:' . $image, // 头像
        ]);

        if ($request->file('avatar')) {
            $image = upload_images($request->file('avatar'), 'avatar', $user->id);
            $attributes['avatar'] = $image->path;
            $avatar_image_id = $image->id;
        }

        $user->update($attributes);

        //查询和清理多余头像
        if ($avatar_image_id > 0) {
            $avatars = DB::table('images')->where('id', '!=', $avatar_image_id)
                ->where('type', '=', 'avatar')
                ->where('user_id', '=', $user->id)
                ->get();
            foreach ($avatars as $avatar) {
                Storage::disk($avatar->disk)->delete($avatar->path);
                DB::table('images')->where('id', '=', $avatar->id)->delete();
            }
        }

        $data['message'] = "头像上传成功";
        return response()->json($data, 200);
    }

    //修改密码
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            //'phone' => 'required|numeric|exists:users',
            'password' => 'required|string|min:6|confirmed', // 需要字段 password_confirmation
        ]);
        $user = $request->user();

        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            abort(403, '验证码已失效');
        }

        if(!hash_equals($verifyData['code'], $request->verification_code)){
            // 返回401
            throw new AuthenticationException('验证码错误');
        }

        if ($user) {
            $user->update(['password' => bcrypt($request->password)]);
            $data['message'] = "密码修改成功";
            return response()->json($data, 200);
        } else {
            $data['message'] = "用户不存在";
            return response()->json($data, 404);
        }
    }

    //编辑用户
    public function update(UserRequest $request)
    {
        $user = $request->user();

        $attributes = $request->only(['phone','nickname','gender']);
        if($request->nickname){
            $bad_nickname = SensitiveWords::getBadWord($request->nickname);
            if(!empty($bad_nickname)){
                $attributes['nickname'] = SensitiveWords::replace($request->nickname,"***"); //替换敏感词为 ***
            }else{
                $attributes['nickname'] = $request->nickname;
            }
        }

        $user->update($attributes);

        $data['message'] = "修改成功";
        return response()->json($data, 200);
    }

    //忘记密码
    public function forgetPassword(Request $request)
    {
        $this->validate($request, [
            //'phone' => 'required|numeric|exists:users',
            'password' => 'required|string|min:6|confirmed', // 需要字段 password_confirmation
        ]);
        $user = User::where('phone','=',$request->phone)->first();

        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            abort(403, '验证码已失效');
        }

        if(!hash_equals($verifyData['code'], $request->verification_code)){
            // 返回401
            throw new AuthenticationException('验证码错误');
        }

        if ($user) {
            $user->update(['password' => bcrypt($request->password)]);
            $data['message'] = "密码修改成功";
            return response()->json($data, 200);
        } else {
            $data['message'] = "用户不存在";
            return response()->json($data, 404);
        }
    }
}
