<?php

namespace App\Services;

use App\Models\Document;
use App\Models\File;
use App\Models\Signature;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

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

    public function sign($id, $params)
    {
        $document = Document::find(4);
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile('storage/app/' . $document->file->path);
        // for ($i = 1; $i < $pageCount; $i++) {
        //     $template = $pdf->importPage($i);
        //     $size = $pdf->getTemplateSize($template);

        //     $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
        //     $pdf->useTemplate($template);

        //     foreach ($params['signatures'] as $signature) {
        //         $widthDiffPercent = ($signature['width'] - $size['width']) / $signature['width'] * 100;
        //         $heightDiffPercent = ($signature['height'] - $size['height']) / $signature['height'] * 100;

        //         $realXPosition = $signature['left'] - ($widthDiffPercent * $signature['left'] / 100);
        //         $realYPosition = $signature['top'] - ($heightDiffPercent * $signature['top'] / 100);
        //     }

        //     if ($i == 1) {
        //         $pdf->Image('storage/app/signatures/60wSAkaFGxFs8EsDU9hNeiDMnXBu6RzntsSMJIPp.png', 50, 50, 50, 50, 'png');
        //     }
        // }
        $signatures = $params['signatures'];
        $signatureCount = count($signatures);
        $currentPage = 1;
        $currentSignatureIdx = 0;
        while ($currentSignatureIdx < $signatureCount && $currentPage <= $pageCount) {
            if ($signatures[$currentSignatureIdx]['page'] > $currentPage) {
                $currentPage++;
            }
            $position = $signatures[$currentSignatureIdx]['position'];
            $info = $signatures[$currentSignatureIdx]['data'];

            $template = $pdf->importPage($currentPage);
            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
            $pdf->useTemplate($template);

            $widthDiffPercent = ($position['width'] - $size['width']) / $position['width'] * 100;
            $heightDiffPercent = ($position['height'] - $size['height']) / $position['height'] * 100;
            $realXPosition = $position['left'] - ($widthDiffPercent * $position['left'] / 100);
            $realYPosition = $position['top'] - ($heightDiffPercent * $position['top'] / 100);

            $signatureFile = File::find($info['id']);
            $pdf->Image('storage/app/' . $signatureFile->path, 50, 50, 50, 50, 'png');
            $currentSignatureIdx++;
        }


        return $pdf->Output('storage/app/sign-documents/new.pdf', 'F');
    }
}
