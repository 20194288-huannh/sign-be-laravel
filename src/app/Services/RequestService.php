<?php

namespace App\Services;

use App\Models\Request;
use App\Models\Signature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RequestService
{
    public function find($id)
    {
        return Request::find($id);
    }
}
