<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'education' => 'array',
                    'experience' => 'array',
                    'teaching_certification' => 'array',
                ];
                break;
            case 'PATCH':
                $userId = auth('api')->id();

                return [
                    'first_name' => 'between:1,25|regex:/^[A-Za-z0-9\-\_]+$/',
                    'last_name' => 'between:1,25|regex:/^[A-Za-z0-9\-\_]+$/',
                    'gender' => 'numeric|in:0,1,2',
                    'country' => 'alpha|min:2',
                    'timezone' => 'timezone',
                    'birthday' => 'date_format:"Y-m-d"',
                    'living_country' => 'alpha|min:2',
                    'mobile' => 'numeric',
                    'country_code' => 'numeric',

                    'spoken' => 'array',
                    'avatar' => 'string',

                    'subject_taught' => 'exists:categories,id',
                    'video' => 'string',
                    'video_thumb_user' => 'string',

                    'education' => 'array',
                    'experience' => 'array',
                    'teaching_certification' => 'array',

                    'currency' => 'string',

//                    'first_name' => 'between:1,25|regex:/^[A-Za-z0-9\-\_]+$/',
//                    'last_name' => 'between:1,25|regex:/^[A-Za-z0-9\-\_]+$/',
//                    'country' => 'alpha|min:2',
//                    'spoken' => 'array',
//                    'subject_taught' => 'exists:categories,id',
//                    'hourly_rate' => 'numeric',
//                    'mobile' => 'numeric',
//                    'introduction' => 'min:50',
//                    'video' => 'file|mimes:mp4',
//                    'timezone' => 'timezone',
//                    'verification_passport' => 'sometimes|mimes:jpeg,bmp,png,gif',
//                    'teaching_certification.*.image' => 'sometimes|mimes:jpeg,bmp,png,gif',
//                    'education.*.image' => 'sometimes|mimes:jpeg,bmp,png,gif',
                ];
                break;
        }
    }
}
