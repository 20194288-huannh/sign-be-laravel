<?php

namespace App\Services;

use App\Models\Document;
use App\Models\File;
use App\Models\Request;
use App\Models\Signature;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

    public function find($id)
    {
        return Document::findOrFail($id);
    }

    public function saveDocument($file)
    {
        $path = Storage::put('documents', $file);
        $filename = $file->getClientOriginalName();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $document = Document::create([
            'sha256' => hash_file('sha256', $file),
            'status' => Document::STATUS_DRAFT,
            'user_id' => auth()->id() ?? 1,
        ]);

        $document->file()->create([
            'name' => $filename,
            'path' => $path,
            'type' => $ext
        ]);

        $userId = auth()->id() ?? 1;

        $user = User::find($userId);

        $user->actions()->create([
            'content' => 'uploaded the document',
            'document_id' => $document->id
        ]);

        return $document;
    }

    public function getByUser(string $status, ?string $filter)
    {
        $statusFilter = explode(',', $status);
        $query = Document::getByUserId(auth()->id() ?? 1);
        if ($status) {
            $query->hasStatus($statusFilter);
        }

        if ($filter) {
            $query->whereHas('file', function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter . '%');
            });
        }
        return $query->paginate();
    }

    public function getAllDocumentOfUser($userId)
    {
        return Document::getByUserId($userId)->get();
    }

    public function history($id)
    {
        return Document::where('id', $id)->first();
    }

    public function getDocumentStatistic()
    {
        $documentCounts = Document::getByUserId(auth()->id() ?? 1)->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        return $documentCounts;
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
                case Signature::TYPE_TEXT:
                    $position = $this->addTextToDocument($pdf, $position, $data, $size, $canvas);
                    $position['page'] = $currentPage;
                    break;
                case 4:
                    echo "i equals 2";
                    break;
            }

            $currentSignatureIdx++;
        }

        $path = 'huan.pdf';
        // $document->signatures()->sync($saveSignatures);
        $pdf->Output('F', $path);
        return $path;
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

        $receivers = $request->receivers()->createMany($users);

        $signatureCollect = collect($signatures)->map(function ($signature) use ($receivers) {
            $receiver = collect($receivers)->where('email', $signature['receiver']['email'])->first();
            return [
                'page' => $signature['page'],
                'width' => $signature['position']['width'],
                'height' => $signature['position']['height'],
                'top' => $signature['position']['top'],
                'left' => $signature['position']['left'],
                'receiver_id' => $receiver['id']
            ];
        });
        $request->requestSignatures()->createMany($signatureCollect);
        return $request;
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

    
    private function addTextToDocument(&$pdf, $position, $data, $size, $canvas)
    {
        $widthDiffPercent = ($canvas['width'] - $size['width']) / $canvas['width'] * 100;
        $heightDiffPercent = ($canvas['height'] - $size['height']) / $canvas['height'] * 100;
        $realXPosition = $position['left'] - ($widthDiffPercent * $position['left'] / 100);
        $realYPosition = $position['top'] - ($heightDiffPercent * $position['top'] / 100);
        $realWidth = $position['width'] *  (1 - $widthDiffPercent / 100);
        $realHeight = $position['height'] *  (1 - $heightDiffPercent / 100);

        $pdf->SetFont('Times','I',18);
        $pdf->setXY($realXPosition, $realYPosition);
        $pdf->Write(0, $data); 

        return [
            'x' => $realXPosition,
            'y' => $realYPosition,
            'width' => $realWidth,
            'height' => $realHeight,
        ];
    }
}
