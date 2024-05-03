<?php

namespace App\Services;

use App\Models\Document;
use App\Models\File;
use App\Models\Request;
use App\Models\Signature;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

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

    public function getByUser(string $status)
    {
        $statusFilter = explode(',', $status);
        return Document::getByUserId(auth()->id() ?? 1)->hasStatus($statusFilter)->paginate();
    }

    public function sign($id, $signatures, $canvas)
    {
        $document = Document::find($id);
        $pdf = new Fpdi();
        // $pageCount = $pdf->setSourceFile('storage/app/' . $document->file->path);
        $file = Storage::get($document->file->path);
        $pageCount = $pdf->setSourceFile(StreamReader::createByString($file));
        $signatureCount = count($signatures);
        $currentPage = 1;
        $currentSignatureIdx = 0;
        $saveSignatures = [];
        $needAddPage = true;
        while ($currentSignatureIdx < $signatureCount && $currentPage <= $pageCount) {
            if ($signatures[$currentSignatureIdx]['page'] > $currentPage) {
                $currentPage++;
                $needAddPage = true;
                continue;
            }

            if ($needAddPage) {
                $template = $pdf->importPage($currentPage);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], array($size['width'], $size['height']));
                $pdf->useTemplate($template);
                $needAddPage = false;
            }

            $position = $signatures[$currentSignatureIdx]['position'];
            $info = $signatures[$currentSignatureIdx]['data'];

            switch ($signatures[$currentSignatureIdx]['type']) {
                case Signature::TYPE_IMAGE:
                    $position = $this->addImageToDocument($pdf, $position, $info, $size, $canvas);
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
        // $document->signatures()->sync($saveSignatures);
        $pdf->Output('signed-documents/huan.pdf', 'F');
        return $document;
    }

    public function sendSign($id, $params)
    {
        $signatures = $params['signatures'];
        $canvas = $params['canvas'];
        $users = $params['users'];
        $email = $params['email'];

        $request = Request::create([
            'document_id' => $id,
            'user_id' => auth()->id() ?? 1,
            'expired_date' => Carbon::parse($email['expired_date']),
            'content' => $email['content'],
            'title' => $email['subject'],
        ]);

        $request->receivers()->createMany($users);

        $signatureCollect = collect($signatures)->map(function ($signature) {
            return [
                'page' => $signature['page'],
                'width' => $signature['position']['width'],
                'height' => $signature['position']['height'],
                'top' => $signature['position']['top'],
                'left' => $signature['position']['left'],
            ];
        });
        $request->requestSignatures()->createMany($signatureCollect);
    }

    private function addImageToDocument(&$pdf, $position, $info, $size, $canvas)
    {
        $widthDiffPercent = ($canvas['width'] - $size['width']) / $canvas['width'] * 100;
        $heightDiffPercent = ($canvas['height'] - $size['height']) / $canvas['height'] * 100;
        $realXPosition = $position['left'] - ($widthDiffPercent * $position['left'] / 100);
        $realYPosition = $position['top'] - ($heightDiffPercent * $position['top'] / 100);
        $realWidth = $position['width'] *  (1 - $widthDiffPercent / 100);
        $realHeight = $position['height'] *  (1 - $heightDiffPercent / 100);

        $signatureFile = File::find($info['id']);
        $pdf->Image(
            $signatureFile->path,
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
