<?php

namespace App\Http\Controllers\Api\Plam;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerCollection;
use App\Http\Requests\Plam\CustomerRequest;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $customer = User::whereIdentify(2)->paginate($request->pageSize ?: 10, ['*'], 'page', $request->page ?: 1);

        return new CustomerCollection($customer);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function store(CustomerRequest $customerRequest, User $user)
    {
        $customer = $user->create([
            'name'      => $customerRequest->name,
            'email'     => $customerRequest->email,
            'password'  => bcrypt($customerRequest->password),
            'identify'  => 2, // 客户
            'school_id' => $customerRequest->school_id,
            'status'    => $customerRequest->status
        ]);

        return new CustomerResource($customer);
    }

    public function update(Request $request, User $user)
    {
        $user->fill($request->all());
        if (!empty($user->password)) {
            $user->password = bcrypt($user->password);
        }
        $user->save();

        return response([
            'code' => 0,
            'msg' => '更新成功'
        ]);
    }
}
