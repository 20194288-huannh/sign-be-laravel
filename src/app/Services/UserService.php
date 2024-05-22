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

    public function storeAction($id, $documentId, $content)
    {
        $user = User::find(auth()->id() ?? 1);

        $user->actions()->create([
            'content' => $content,
            'document_id' => $documentId
        ]);
    }
}
