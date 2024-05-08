<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getByEmail($email)
    {
        return User::getByEmail($email)->first();
    }

    public function register($params)
    {
        return User::create($params);
    }

    public function getById($id)
    {
        return User::find($id);
    }
}
