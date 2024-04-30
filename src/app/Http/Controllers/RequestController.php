<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestDetailResource;
use App\Services\RequestService;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function __construct(public RequestService $requestService)
    {
        
    }

    public function show($id)
    {
        $request = $this->requestService->find($id);
        return response()->ok(new RequestDetailResource($request));
    }
}
