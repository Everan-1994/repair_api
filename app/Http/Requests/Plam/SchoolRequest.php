<?php

namespace App\Http\Requests\Plam;

use App\Http\Requests\Request;

class SchoolRequest extends Request
{

    public function rules()
    {
        return [
            'school_name' => 'required|string',
            'school_code' => 'required||unique:schools',
            'logo'        => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'school_name.required' => '请填写学校名称',
            'school_code.required' => '请填写学校代码',
            'school_code.unique'   => '该学校已存在',
            'logo.required'        => '请上次学校LOGO',
        ];
    }
}
