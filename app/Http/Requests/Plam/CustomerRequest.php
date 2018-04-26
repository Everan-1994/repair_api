<?php

namespace App\Http\Requests\Plam;

use App\Http\Requests\Request;

class CustomerRequest extends Request
{
    public function rules()
    {
        return [
            'name'      => 'required|string',
            'email'     => 'required|unique:users|email',
            'school_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required'      => '请填写昵称',
            'email.required'     => '请填写邮箱',
            'email.email'        => '邮箱格式不正确',
            'email.unique'       => '邮箱已存在',
            'school_id.required' => '请选择绑定的学习'
        ];
    }
}
