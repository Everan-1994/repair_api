<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\Request;

class WeappAuthorizationRequest extends Request
{
    public function rules()
    {
        return [
            'code' => 'required|string',
        ];
    }
}
