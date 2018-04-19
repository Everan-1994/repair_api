<?php

namespace App\Http\Controllers\Api\Plam;

use App\Http\Requests\Plam\SchoolRequest;
use App\Http\Resources\SchoolResource;
use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Resources\SchoolCollection;

class SchoolsController extends Controller
{
    public function index(Request $request)
    {
        $school = School::paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return new SchoolCollection($school);
    }

    public function store(SchoolRequest $schoolRequest, School $school)
    {
        $sl = $school->create($schoolRequest->all());

        return new SchoolResource($sl);
    }
}
