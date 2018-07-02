<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Http\Request;

class TypesController extends Controller
{
    public function index(Request $request)
    {
        $type = Type::paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return TypeResource::collection($type);
    }

    public function store(Type $type, Request $request)
    {
        $tp = $type->create([
            'name'      => $request->name,
            'school_id' => $request->school_id,
        ]);

        return new TypeResource($tp);
    }

    public function show(Type $type)
    {
        return new TypeResource($type);
    }

    public function update(Request $request, Type $type)
    {
        $type->fill($request->all());
        $type->save();

        return response([
            'code' => 0,
            'msg' => '更新成功'
        ]);
    }

    /**
     * 类型比率
     */
    public function getOrderTypeRatio(Request $request, Type $type)
    {
        $list = $type->where('school_id', $request->school_id)->with('orders')->get();

        $data = [];
        if ($list) {
            foreach ($list as $key => $val) {
                $data[$key] = [
                    'type' => $val['name'],
                    'count' => count($val['orders'])
                ];
            }
        }

        return response($data);
    }

    public function del(Type $type)
    {
        $type->delete();

        return response([
            'code' => 0,
            'msg'  => 'Successed'
        ], 204);
    }
}
