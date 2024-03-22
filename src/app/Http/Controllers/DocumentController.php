<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    public function __construct(private DocumentService $documentService)
    {
    }

    public function store(CreateDocumentRequest $request)
    {
        $signature = $this->documentService->create($request);
        return response()->ok();
    }

    public function index()
    {
        $documents = $this->documentService->getByUser();
        return response()->ok(DocumentResource::collection($documents));
    }
}
