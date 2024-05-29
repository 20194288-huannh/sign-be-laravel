<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\DocumentService;

class NotificationController extends Controller
{
    public function __construct(public DocumentService $documentService)
    {
    }

    public function index()
    {
        $documents = $this->documentService->getAllDocument(auth()->id() ?? 1);
        $notifications = Notification::whereIn('document_id', $documents->pluck('id'))->latest()->get();
        return response()->ok(NotificationResource::collection($notifications));
    }

    public function destroy(int $id)
    {
        Notification::where('id', $id)->delete();
        return response()->ok();
    }
}
