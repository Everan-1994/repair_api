<?php

namespace App\Http\Controllers\Api;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\SchoolResource;
use App\Http\Resources\SchoolCollection;
use App\Http\Requests\Plam\SchoolRequest;

class SchoolsController extends Controller
{
    public function index(Request $request)
    {
        $school = School::paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return new SchoolCollection($school);
    }

    public function store(SchoolRequest $schoolRequest, School $school)
    {
        $sl = $school->create([
            'logo'        => $schoolRequest->logo,
            'status'      => $schoolRequest->status,
            'school_name' => $schoolRequest->school_name,
            'school_code' => $schoolRequest->school_code,
        ]);

        return new SchoolResource($sl);
    }

    public function update(Request $request, School $school)
    {
        $school->fill($request->all());
        $school->save();

        // 更新头像
        User::where(['identify' => 2, 'school_id' => $school['id']])->update([
            'avatar' => $school->logo,
            'updated_at' => now()->toDateTimeString()
        ]);

        return response([
           'code' => 0,
           'msg' => '更新成功'
        ]);
    }

    public function show(School $school)
    {
        return new SchoolResource($school);
    }

    public function destroy(School $school)
    {
        $this->authorize('destroy', $school);
        $school->delete();

        return response([
            'code' => 0,
            'msg' => '删除成功'
        ]);
    }

    public function getSchoolList()
    {
        $school = School::select('id', 'school_name', 'bind')->get();

        return response($school);
    }
}
