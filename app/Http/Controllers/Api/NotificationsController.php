<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $notifications =  \Auth::user()->notifications()->paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return NotificationResource::collection($notifications);
    }

    public function stats()
    {
        return response([
            'unread_count' => \Auth::user()->notification_count,
        ]);
    }

    public function read()
    {
        \Auth::user()->markAsRead();

        return response([
            'msg' => 'success'
        ], 204);
    }
}
