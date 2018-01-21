<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseControllers\BaseCommonController;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends BaseCommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = User::paginate($this->perPage);
        $data = collect($data->toArray());

        return $this->showAll($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name'          => 'required',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:6|confirmed',
        ];
        $this->validate($request, $rules);
        $data = $request->all();

        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        $user->generateVerificationToken();

        return $this->showOne($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name'          => 'min:1',
            'password'      => 'min:6|confirmed',
        ]);

        $user->fill($request->only([
            'name',
            'password',
        ]));

        if($request->has('password')) {
            $user->password = bcrypt($user->password);
        }

        if($user->isClean()) {
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $user->save();
        return $this->showOne($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->showOne($user);
    }
}
