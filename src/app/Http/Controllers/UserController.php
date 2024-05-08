<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(public UserService $userService)
    {
        
    }

    public function show($id)
    {
        return response()->ok(new UserResource($this->userService->getById($id)));
    }

    public function getByEmail(Request $request)
    {
        return response()->ok(new UserResource($this->userService->getByEmail($request->email)));
    }
}
