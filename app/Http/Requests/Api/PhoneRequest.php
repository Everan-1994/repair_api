<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class PhoneRequest extends Request
{
    public function rules()
    {
        return [
            'phone' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '手机号不能为空'
        ];
    }
}
