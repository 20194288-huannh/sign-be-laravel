<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function view($id)
    {
        $file = File::find($id);
        return Storage::download($file->path);
    }
}
