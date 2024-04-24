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
        $signatures = $params['signatures'];
        $signatureCount = count($signatures);
        $currentPage = 1;
        $currentSignatureIdx = 0;
        $saveSignatures = [];
        while ($currentSignatureIdx < $signatureCount && $currentPage <= $pageCount) {
            if ($signatures[$currentSignatureIdx]['page'] > $currentPage) {
                $currentPage++;
            }
            $position = $signatures[$currentSignatureIdx]['position'];
            $info = $signatures[$currentSignatureIdx]['data'];
            $scale = $signatures[$currentSignatureIdx]['scale'];

            $template = $pdf->importPage($currentPage);
            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
            $pdf->useTemplate($template);
            switch ($signatures[$currentSignatureIdx]['type']) {
                case Signature::TYPE_IMAGE:
                    $position = $this->addImageToDocument($pdf, $position, $scale, $info, $size);
                    $position['page'] = $currentPage;
                    $saveSignatures[$info['id']] = $position;
                    break;
                case 3:
                    echo "i equals 1";
                    break;
                case 4:
                    echo "i equals 2";
                    break;
            }

            $currentSignatureIdx++;
        }
        $document->signatures()->sync($saveSignatures);

        $document
        return $pdf->Output('storage/app/sign-documents/new.pdf', 'F');
    }

    private function addImageToDocument(&$pdf, $position, $scale, $info, $size)
    {
        $widthDiffPercent = ($position['width'] - $size['width']) / $position['width'] * 100;
        $heightDiffPercent = ($position['height'] - $size['height']) / $position['height'] * 100;
        $realXPosition = $position['left'] - ($widthDiffPercent * $position['left'] / 100);
        $realYPosition = $position['top'] - ($heightDiffPercent * $position['top'] / 100);
        $realWidth = $position['width'] / $scale *  (1 - $widthDiffPercent / 100);
        $realHeight = $position['height'] / $scale *  (1 - $heightDiffPercent / 100);

        $signatureFile = File::find($info['id']);
        $pdf->Image(
            'storage/app/' . $signatureFile->path,
            $realXPosition,
            $realYPosition,
            $realWidth,
            $realHeight,
            'png'
        );
        return [
            'x' => $realXPosition,
            'y' => $realYPosition,
            'width' => $realWidth,
            'height' => $realHeight,
        ];
    }
}
