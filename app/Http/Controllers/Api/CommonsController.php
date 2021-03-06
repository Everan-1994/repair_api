<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommonsController extends Controller
{
    public function upload(Request $request)
    {
        $result = Storage::disk('upyun')->put('/', $request->file('images'));

        return response()->json([
            'code' => 0,
            'url'  => $result
        ]);
    }

    // 删除又拍云上的图片
    public function delImage($path)
    {
        return Storage::disk('upyun')->delete('/' . $path);
    }
}
