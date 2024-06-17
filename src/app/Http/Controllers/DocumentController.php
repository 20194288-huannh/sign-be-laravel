<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Requests\SaveDocumentRequest;
use App\Http\Requests\SaveSignOwnDocumentRequest;
use App\Http\Resources\ActionResource;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\HistoryResource;
use App\Models\Document;
use App\Models\File;
use App\Models\Notification;
use App\Models\Receiver;
use App\Models\Request as ModelsRequest;
use App\Models\SendSignToken;
use App\Models\User;
use App\Services\ActionService;
use App\Services\DocumentService;
use App\Services\RequestService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Snowfire\Beautymail\Beautymail;

class DocumentController extends Controller
{
    public function __construct(
        private DocumentService $documentService,
        private UserService $userService,
        private RequestService $requestService,
        private ActionService $actionService
    ) {
    }

    public function store(CreateDocumentRequest $request)
    {
        $document = $this->documentService->create($request);
        return response()->ok();
    }

    public function getDocumentByUser(Request $request)
    {
        $documents = $this->documentService->getByUser($request->status, $request->filter);
        return response()->ok(new DocumentCollection($documents));
    }

    public function save(SaveDocumentRequest $request)
    {
        // Get file info
        $path = Storage::put('documents', $request->file);
        $filename = $request->file->getClientOriginalName();
        $sha = hash_file('sha256', $request->file);

        // Store file info
        $document = $this->documentService->saveDocument($path, $filename, $sha, null, Document::STATUS_DRAFT, null);

        // Store action upload file of user
        $this->userService->storeAction(auth()->id() ?? 1, $document->id, ' uploaded the document');
        return response()->ok(new DocumentResource($document));
    }

    public function saveSignOwn(SaveSignOwnDocumentRequest $request, int $id)
    {
        // Get file info
        $path = Storage::put('documents', $request->file);
        $filename = $request->file->getClientOriginalName();

        // Store file info
        $document = $this->documentService->saveDocument($path, $filename, $request->sha, $id, Document::STATUS_COMPLETED, null);

        // Store action upload file of user
        $this->userService->storeAction(auth()->id() ?? 1, $document->id, ' sign own');
        return response()->ok(new DocumentResource($document));
    }

    public function index(Request $request)
    {
        $documents = $this->documentService->getByUser($request->status, $request->filter);
        return response()->ok(DocumentResource::collection($documents));
    }

    public function show(int $id)
    {
        $document = $this->documentService->find($id);
        return response()->ok(new DocumentResource($document));
    }

    public function getDocumentStatistic()
    {
        $data = $this->documentService->getDocumentStatistic();
        return response()->ok($data);
    }

    public function sendSign($id, Request $sendSignRequest)
    {
        $data = $this->documentService->sendSign($id, $sendSignRequest->all());

        $content = ' send email to ';
        foreach ($data['receivers'] as $receiver) {
            $content = $content . $receiver->name . '<' . $receiver->email . '>, ';
        }
        $this->userService->storeAction(
            auth()->user() ?? 1,
            $data['newDocument']['id'],
            "$content."
        );
        $document = $this->documentService->find($id);
        $this->sendMailNeedSignature($sendSignRequest, $document, $data['request']);
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

            $pattern = '/SENDER_NAME|SENDER_EMAIL_ID|DOCUMENT_NAME/';

            // Hàm callback để thay thế các phần tử
            $replacement = function ($matches) use ($document) {
                switch ($matches[0]) {
                    case 'SENDER_NAME':
                        return auth()->user()->name;
                    case 'SENDER_EMAIL_ID':
                        return auth()->user()->email;
                    case 'DOCUMENT_NAME':
                        return $document->file->name;
                }
            };

            $title = preg_replace_callback($pattern, $replacement, $request->title);
            $beautymail = app()->make(Beautymail::class);
            $beautymail->send('mails.sign', [
                'document' => $document,
                'sender' => [
                    'name' => 'Huan Sender'
                ],
                'url' => config('app.fe_url') . 'signed-document?token=' . $token,
                'signer' => $signer
            ], function ($message) use ($signer, $ccEmail, $request, $title) {
                if (count($ccEmail)) {
                    $message
                        ->from('huan.nh194288@sis.hust.edu.vn')
                        ->to($signer['email'], $signer['name'])
                        ->cc(...$ccEmail)
                        ->subject($title);
                } else {
                    $message
                        ->from('huan.nh194288@sis.hust.edu.vn')
                        ->to($signer['email'], $signer['name'])
                        ->subject($title);
                }
            });
        }
        return;
    }

    public function sign($id, Request $request)
    {
        $document = Document::find($id);
        $token = $request->token;
        $data = (object) json_decode(Crypt::decryptString($token));
        $requestInstace = $this->requestService->find($data->request_id, $data->email);
        $receiver = $requestInstace->receivers()->where('email', $data->email)->first();

        $path = $this->documentService->sign($id, $request->signatures, $request->canvas);
        $this->documentService->saveDocument(
            $path,
            $document->file->name,
            hash_file('sha256', $path),
            $id,
            Document::STATUS_IN_PROGRESS,
            $requestInstace
        );
        $document->update(['is_show' => 0]);

        // Ký
        $receiver->actions()->updateOrCreate([
            'content' => "<$receiver->email> signed the document",
            'document_id' => $requestInstace->document_id
        ], []);

        $receiver->actions()->updateOrCreate([
            'content' => "<$receiver->email> completed the document",
            'document_id' => $requestInstace->document_id
        ], []);
        $receiver->update(['status' => Receiver::STATUS_COMPLETED]);

        Notification::create([
            'receiver_id' => $receiver->id,
            'content' => 'Signed a document',
            'document_id' => $document->id,
            'status' => Notification::STATUS_COMPLETED
        ]);
        $receiver->actions()->updateOrCreate([
            'content' => "<$receiver->email> signed the document",
            'document_id' => $document->id
        ], []);

        $this->isCompletedDocument($requestInstace);

        // SendSignToken::where('token', $token)->delete();
        return Storage::download($path);
    }

    private function isCompletedDocument($request)
    {
        $isCompleted = true;
        foreach ($request->receivers as $receiver) {
            if ($receiver->status !== Receiver::STATUS_COMPLETED) {
                $isCompleted = false;
                break;
            }
        }

        if ($isCompleted) {
            $request->documents()->isShow()->update(['status' => Document::STATUS_COMPLETED]);
        }
    }

    public function save1($id)
    {
        $file = File::find($id);
        return Storage::download($file->path);
    }

    public function signOwn(Request $request, $id)
    {
        $path = $this->documentService->sign($id, $request->signatures, $request->canvas);

        return Storage::download($path);
    }

    public function history($sha)
    {
        $data = [];
        $history = $this->documentService->history($sha);
        $this->flatNestedDocuments($history, $data);
        return response()->ok(DocumentResource::collection($data));
    }

    public function flatNestedDocuments($document, &$array)
    {
        if (!$document) {
            return;
        }
        if (!$document->parent_id) {
            array_push($array, $document);
            return;
        }
        array_push($array, $document);
        $this->flatNestedDocuments($document->parent, $array);
        return;
    }

    public function getParentIdOfDocument($id)
    {
        $data = [];
        $document = $this->documentService->find($id);
        $this->flatNestedDocuments($document, $data);
        return $data;
    }

    public function getActionsOfDocument($id)
    {
        $data = $this->getParentIdOfDocument($id);
        $documentIds = collect($data)->pluck('id');
        $actions = $this->actionService->getActionOfDocuments($documentIds);
        return response()->ok(ActionResource::collection($actions));
    }
}
