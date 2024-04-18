<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Signature;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function create($request)
    {
        $path = Storage::put('documents', $request->file);
        $filename = $request->file->getClientOriginalName();
        $signature = Document::create([
            'sha256_original_file' => hash_file('sha256', storage_path($path)),
            'type' => random_int(1, 5),
            'user_id' => auth()->id()
        ]);

        $signature->file()->create([
            'name' => 'storage/' . $filename,
            'path' => $path
        ]);
    }

    public function saveDocument($file)
    {
        $path = Storage::put('documents', $file);
        $filename = $file->getClientOriginalName();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $document = Document::create([
            'sha256' => hash_file('sha256', $file),
            'status' => Document::STATUS_DRAFT,
            // 'user_id' => auth()->id()
            'user_id' => 1,
        ]);

        $document->file()->create([
            'name' => $filename,
            'path' => $path,
            'type' => $ext
        ]);

        return $document;
    }

    public function getByUser()
    {
        return Document::getByUserId(auth()->id() ?? 1)->paginate();
    }
}
