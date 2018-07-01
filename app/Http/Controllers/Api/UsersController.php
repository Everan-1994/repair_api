<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    public function info()
    {
        $user = User::whereId(\Auth::guard('api')->id())->with('school')->first();

        return new UserResource($user);
    }

    /**
     * 用户总数
     */
    public function getUserCount(Request $request)
    {
        $count = User::where('school_id', $request->school_id)->count();

        return response(['count' => $count]);
    }
}
