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
        $receiver = $request->receivers()->where('email', 'gundamakp01@gmail.com')->first();
        $receiver->actions()->create([
            'content' => 'uploaded the document',
            'document_id' => $request->document_id
        ]);
        return response()->ok(new RequestDetailResource($request));
    }
}
