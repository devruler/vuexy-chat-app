<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function updateCurrentUser(UpdateUserRequest $request){

        return auth()->user()->update($request->validated());
    }
}
