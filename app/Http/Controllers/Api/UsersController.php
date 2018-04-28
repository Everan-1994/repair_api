<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;

class UsersController extends Controller
{
    public function info()
    {
        $user = User::whereId(\Auth::id())->with('school')->first();

        return new UserResource($user);
    }
}
