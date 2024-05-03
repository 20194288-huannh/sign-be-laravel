<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\SaveDocumentRequest;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\File;
use App\Models\Receiver;
use App\Services\DocumentService;
use Illuminate\Http\Request;
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

    public function getDocumentByUser($id, Request $request)
    {
        $documents = $this->documentService->getByUser($request->status);
        return response()->ok(new DocumentCollection($documents));
    }

    public function save(SaveDocumentRequest $request)
    {
        $document = $this->documentService->saveDocument($request->file);
        return response()->ok(new DocumentResource($document));
    }

    public function index(Request $request)
    {
        $documents = $this->documentService->getByUser($request->status);
        return response()->ok(DocumentResource::collection($documents));
    }

    public function sendSign($id, Request $request)
    {
        // $document = $this->documentService->sign($id, $request->signatures, $request->canvas);
        $result = $this->documentService->sendSign($id, $request->all());
        // $beautymail = app()->make(Beautymail::class);
        // $users = collect($request->users);
        // $signer = $users->where('type', Receiver::TYPE_SIGNER)->first();
        // $ccEmail = $users->where('type', Receiver::TYPE_CC)->pluck('email')->all();
        // $beautymail->send('mails.sign', [
        //     'document' => $document,
        //     'sender' => [
        //         'name' => 'Huan Sender'
        //     ],
        //     'signer' => $signer
        // ], function ($message) use ($signer, $ccEmail) {
        //     $message
        //         ->from('huan.nh194288@sis.hust.edu.vn')
        //         ->to($signer['email'], $signer['name'])
        //         ->cc(...$ccEmail)
        //         ->subject('Needs Your Signature for the Documents!');
        // });
    }

    public function sign($id, Request $request)
    {
        $this->documentService->sign($id, $request->signatures, $request->canvas);
        return response()->ok();
    }

    public function save1($id)
    {
        $file = File::find($id);
        return Storage::download($file->path);
    }

    public function signOwn(Request $request, $id)
    {
        $this->documentService->sign($id, $request->signatures, $request->canvas);
        return response()->ok();
    }
}
