<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\SaveDocumentRequest;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\File;
use App\Models\Receiver;
use App\Models\SendSignToken;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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

    public function getDocumentStatistic()
    {
        $data = $this->documentService->getDocumentStatistic();
        return response()->ok($data);
    }

    public function sendSign($id, Request $sendSignRequest)
    {
        $request = $this->documentService->sendSign($id, $sendSignRequest->all());
        $document = $this->documentService->find($id);
        $this->sendMailNeedSignature($sendSignRequest, $document, $request);
    }

    private function sendMailNeedSignature($sendSignRequest, $document, $request)
    {
        $users = collect($sendSignRequest->users);
        $signers = $users->where('type', Receiver::TYPE_SIGNER)->all();
        $ccEmail = $users->where('type', Receiver::TYPE_CC)->pluck('email')->all();

        foreach ($signers as &$signer) {
            $token = Crypt::encryptString(json_encode([
                'email' => $signer['email'],
                'request_id' => $request->id
            ]));
            SendSignToken::create([
                'request_id' => $request->id,
                'token' => $token
            ]);
            $beautymail = app()->make(Beautymail::class);
            $beautymail->send('mails.sign', [
                'document' => $document,
                'sender' => [
                    'name' => 'Huan Sender'
                ],
                'url' => config('app.fe_url') . 'signed-document?token=' . $token,
                'signer' => $signer
            ], function ($message) use ($signer, $ccEmail) {
                if (count($ccEmail)) {
                    $message
                        ->from('huan.nh194288@sis.hust.edu.vn')
                        ->to($signer['email'], $signer['name'])
                        ->cc(...$ccEmail)
                        ->subject('Needs Your Signature for the Documents!');
                } else {
                    $message
                        ->from('huan.nh194288@sis.hust.edu.vn')
                        ->to($signer['email'], $signer['name'])
                        ->subject('Needs Your Signature for the Documents!');
                }
            });
        }
        return;
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
