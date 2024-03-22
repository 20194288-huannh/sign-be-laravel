<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSignatureRequest;
use App\Services\SignatureService;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function __construct(private SignatureService $signatureService)
    {
    }

    public function store(CreateSignatureRequest $request)
    {
        $signature = $this->signatureService->create($request);
        return response()->ok();
    }

    public function index()
    {
        $signatures = $this->signatureService->getByUser();
    }
}
