<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class PhoneRequest extends Request
{
    public function rules()
    {
        return [
            'phone' => 'required|regex:/^1[3456789]\d{9}$/|unique:users',
        ];
    }

    public function messages()
    {
        return [
            'phone.unique' => '手机号码已被使用'
        ];
    }
}
