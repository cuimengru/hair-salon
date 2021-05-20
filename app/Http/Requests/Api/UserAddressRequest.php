<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
/*    public function authorize()
    {
        return false;
    }*/

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'province' => 'required', // 省
            'city' => 'required', // 市
            'district' => 'required', // 区
            'street' => 'required', // 街道
            'address' => 'required', // 具体地址
            'contact_name' => 'required', // 联系人姓名
            'contact_phone' => 'required', // 联系人电话
        ];
    }
}
