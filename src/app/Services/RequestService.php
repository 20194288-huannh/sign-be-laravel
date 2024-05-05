<?php

namespace App\Services;

use App\Models\Request;
use App\Models\Signature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RequestService
{
    public function find($id, $email)
    {
        return Request::with(['requestSignatures' => function ($query) use ($email) {
            $query->whereHas('receiver', function ($q) use ($email) {
                $q->where('email', $email);
            });
            // $query->where('request_id', 2);
        }])->find($id);
    }
}
