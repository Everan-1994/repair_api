<?php

namespace App\Http\Controllers\Api;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Resources\AreaResource;
use App\Http\Requests\Plam\AreaRequest;

class AreasController extends Controller
{
    public function index(Request $request)
    {
        $area = Area::whereSchoolId($request->school_id)
            ->paginate(10, ['*'], 'page', $request->page ?: 1);

        return AreaResource::collection($area);
    }

    public function store(AreaRequest $areaRequest, Area $area)
    {
        $area = $area->create([
            'name'      => $areaRequest->name,
            'school_id' => $areaRequest->school_id,
            'status'    => $areaRequest->status
        ]);

        return new AreaResource($area);
    }

    public function update(AreaRequest $areaRequest, Area $area)
    {
        $area->update([
            'name'   => $areaRequest->name,
            'status' => $areaRequest->status
        ]);

        return response([
            'msg' => 'success'
        ]);
    }

    public function show(Area $area)
    {
        return new AreaResource($area);
    }

    public function changeStatus(Request $request, Area $area)
    {
        $area->update([
            'status'     => $request->status,
            'updated_at' => now()->toDateTimeString()
        ]);

        return response([
            'code' => 0,
            'msg'  => '更新成功'
        ]);
    }

    public function areaList(Request $request)
    {
        $area = Area::whereSchoolId($request->school_id)->whereStatus(1)->select('id', 'name')->get();

        return response($area);
    }

    public function del(Area $area)
    {
        $area->delete();

        return response([
            'code' => 0,
            'msg'  => 'Successed'
        ], 204);
    }
}
