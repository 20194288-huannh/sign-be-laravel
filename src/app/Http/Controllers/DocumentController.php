<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\SaveDocumentRequest;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\File;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Storage;
use Snowfire\Beautymail\Beautymail;

class DocumentController extends Controller
{
    public function __construct(private DocumentService $documentService)
    {
    }

    public function store(CreateDocumentRequest $request)
    {
        $document = $this->documentService->create($request);
        return response()->ok();
    }

    public function getDocumentByUser($id)
    {
        $documents = $this->documentService->getByUser($id);
        return response()->ok(new DocumentCollection($documents));
    }

    public function save(SaveDocumentRequest $request)
    {
        $document = $this->documentService->saveDocument($request->file);
        return response()->ok(new DocumentResource($document));
    }

    public function index()
    {
        $documents = $this->documentService->getByUser();
        return response()->ok(DocumentResource::collection($documents));
    }

    public function sign()
    {
        $beautymail = app()->make(Beautymail::class);
        $beautymail->send('mails.sign', [], function($message)
        {
            $message
                ->from('bar@example.com')
                ->to('foo@example.com', 'John Smith')
                ->subject('Welcome!');
        });
    }

    public function save1($id)
    {
        $file = File::find($id);
        return Storage::download($file->path);
    }
}
