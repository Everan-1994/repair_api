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
}
