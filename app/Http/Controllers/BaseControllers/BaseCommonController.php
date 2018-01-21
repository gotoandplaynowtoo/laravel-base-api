<?php

namespace App\Http\Controllers\BaseControllers;

use App\Traits\Responses\BaseCommonResponse;
use App\Http\Controllers\Controller;

class BaseCommonController extends Controller
{
    use BaseCommonResponse;
    protected $perPage = 10;

    protected function showNoChangesError() {
        return $this->errorResponse('You need to specify a different value to update', 422);
    }

    protected function showUnauthorizedError() {
        return $this->errorResponse('You are unauthorized to access this resource', 401);
    }
}
