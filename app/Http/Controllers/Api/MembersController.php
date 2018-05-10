<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class MembersController extends Controller
{
    public function index(Request $request)
    {
        if (!$school_id = \Cache::get('user_school_id_' . \Auth::id())) {
            $school_id = User::whereId(\Auth::id())->value('school_id');
            \Cache::set('user_school_id_' . \Auth::id(), $school_id);
        }

        $user = User::whereSchoolId($school_id)
            ->when(isset($request->status), function ($query) use ($request) {
                return $query->whereStatus($request->status);
            })
            ->whereIdentify(5)
            ->orderBy($request->order ?: 'created_at', $request->sort ?: 'desc')
            ->paginate($request->pageSize, ['*'], 'page', $request->page ?: 1);

        return UserResource::collection($user);
    }
}
