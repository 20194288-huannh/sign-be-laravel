<?php

namespace App\Services;

use App\Models\User;

class UserService extends BaseService
{
    public function model()
    {
        return User::class;
    }

    public function getByEmail($email)
    {
        return $this->model->getByEmail($email)->first();
    }
}
