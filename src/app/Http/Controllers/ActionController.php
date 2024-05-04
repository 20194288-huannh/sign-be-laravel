<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActionResource;
use App\Services\ActionService;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function __construct(
        public DocumentService $documentService,
        public ActionService $actionService
    ) {
    }

    public function index()
    {
        $documentIds = $this->documentService->getAllDocumentOfUser(auth()->id() ?? 1)->pluck('id');
        $actions = $this->actionService->getActionOfDocuments($documentIds);
        return response()->ok(ActionResource::collection($actions));
    }
}
