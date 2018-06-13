<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class OrderRequest extends Request
{
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'school_id' => 'required',
                    'area_id'   => 'required',
                    'type'      => 'required',
                    'address'   => 'required',
                    'contents'  => 'required'
                ];
                break;
            case 'PUT':
                return [
                    'area_id'  => 'required',
                    'type'     => 'required',
                    'address'  => 'required',
                    'contents' => 'required'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'school_id.required' => '请选择组织',
            'area_id.required'   => '请选择区域',
            'type.required'      => '请选择申报类型',
            'address.required'   => '请填写地址',
            'contents.required'  => '请填写事项'
        ];
    }
}
