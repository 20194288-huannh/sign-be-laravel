<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Signature;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function create($request)
    {
        // $path = Storage::put('signatures', $request->file);
        // $filename = $request->file->getClientOriginalName();
        // $signature = Signature::create([
        //     'sha256_original_file' => '6b148b743bc6620205540594150945f160197fba45e9e0de16a00a343abca660',
        //     'type' => random_int(1, 5),
        //     'user_id' => auth()->id()
        // ]);

        // $signature->file()->create([
        //     'name' => 'storage/' . $filename,
        //     'path' => $path
        // ]);
    }

    public function getByUser()
    {
        return Document::getByUserId(auth()->id())->get();
    }
}
