<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    //'nickname' => 'between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name',
                    'password' => 'required|alpha_dash|min:6',
                    'verification_key' => 'required|string',
                    'verification_code' => 'required|string',
                ];
                break;
            case 'PATCH':
                $userId = auth('api')->id();

                return [
                    'phone' => 'numeric|unique:users',
                    'nickname' => 'string|between:3,25|unique:users',
                    'gender' => 'numeric',
                ];
                break;
        }
        return [

        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }

    public function messages()
    {
        return [
            'nickname.unique' => '昵称已被占用，请重新填写',
            'nickname.between' => '昵称必须介于 3 - 25 个字符之间。',

        ];
    }
}
