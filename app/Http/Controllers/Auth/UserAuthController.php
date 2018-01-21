<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseControllers\BaseCommonController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthController extends BaseCommonController
{
    use AuthenticatesUsers;

    public function __construct() {
        $this->middleware('guest:api-user-login')
            ->except(['logout']);
    }
    public function login(Request $request) {

        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            if(!$user->verified) {
                return $this->errorResponse('Your account is not yet verified.', 422);
            }
            $user->generateToken();

            $user->token = $user->getApiToken();

            return $this->showOne($user);
        }

        return $this->errorResponse('Authentication failed.', 422);

    }

    public function logout() {
        $user = Auth::guard('api-user')->user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }
        return $this->showOne('Successfully logged out');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('api-user-login');
    }
}
