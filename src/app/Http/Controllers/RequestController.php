<?php

namespace App\Http\Controllers;

use App\Http\Resources\RequestDetailResource;
use App\Models\Notification;
use App\Models\Receiver;
use App\Services\NotificationService;
use App\Services\RequestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

class RequestController extends Controller
{
    public function __construct(
        public RequestService $requestService,
        public NotificationService $notificationService
    ) {
    }

    public function show(Request $request)
    {
        try {
            $data = (object) json_decode(Crypt::decryptString($request->token));
        } catch (Exception $e) {
            return response()->error(
                Response::HTTP_NOT_FOUND,
                '要求されたリソースはシステムに存在しません。'
            );
        }
        $request = $this->requestService->find($data->request_id, $data->email);
        $document = $request->documents()->isShow()->first();
        $receiver = $request->receivers()->where('email', $data->email)->first();
        $receiver->actions()->updateOrCreate([
            'content' => "<$receiver->email> viewed the document",
            'document_id' => $document->id
        ], []);
        $this->notificationService->updateOrCreate([
            'receiver_id' => $receiver->id,
            'content' => 'Viewed a document',
            'document_id' => $document->id,
            'status' => Notification::STATUS_IN_PROGRESS
        ]);
        $receiver->update(['status' => Receiver::STATUS_VIEWED]);

        return response()->ok(new RequestDetailResource($request));
    }
}
