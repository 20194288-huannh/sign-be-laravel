<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestDetailResource;
use App\Services\RequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RequestController extends Controller
{
    public function __construct(public RequestService $requestService)
    {
    }

    public function show(Request $request)
    {
        $data = (object) json_decode(Crypt::decryptString($request->token));
        $request = $this->requestService->find($data->request_id, $data->email);
        $receiver = $request->receivers()->where('email', $data->email)->first();
        $receiver->actions()->create([
            'content' => 'viewed documents',
            'document_id' => $request->document_id
        ]);
        return response()->ok(new RequestDetailResource($request));
    }
}
