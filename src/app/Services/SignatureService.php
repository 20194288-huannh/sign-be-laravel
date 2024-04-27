<?php

namespace App\Services;

use App\Models\Signature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SignatureService
{
    public function create($request)
    {
        $path = Storage::put('signatures', $request->signature);
        $filename = $request->signature->getClientOriginalName();
        $signature = Signature::create([
            'sha256_original_file' => '6b148b743bc6620205540594150945f160197fba45e9e0de16a00a343abca660',
            'type' => random_int(1, 5),
            'user_id' => auth()->id() ?? 1
        ]);

        $signature->file()->create([
            'name' => 'storage/' . $filename,
            'path' => $path
        ]);

        return $signature;
    }

    public function getByUser()
    {
        return Signature::getByUserId(auth()->id() ?? 1)->latest()->get();
    }

    public function delete($id)
    {
        $signature = Signature::find($id);
        // Storage::delete($signature->file->path);
        return DB::transaction(function () use ($signature) {
            $signature->file()->delete();
            return $signature->delete();
        });
    }
}
