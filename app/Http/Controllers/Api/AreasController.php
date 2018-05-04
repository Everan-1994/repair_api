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

    public function areaList(Request $request)
    {
        $area = Area::whereSchoolId($request->school_id)->whereStatus(1)->select('id', 'name')->get();

        return response($area);
    }
}
