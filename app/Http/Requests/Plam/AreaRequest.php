<?php

namespace App\Http\Requests\Plam;

use App\Http\Requests\Request;

class AreaRequest extends Request
{
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                $rules = [
                    'name'      => 'required',
                    'school_id' => 'required'
                ];
                break;
            case 'PATCH':
                $rules = [
                    'name'      => 'required'
                ];
                break;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required'      => '请填写区域名称',
            'school_id.required' => '请填写学校id'
        ];
    }
}
