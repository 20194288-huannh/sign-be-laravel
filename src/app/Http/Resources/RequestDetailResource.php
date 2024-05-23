<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'users' => ReceiverResource::collection($this->receivers),
            'email' => [
                'subject' => $this->title,
                'expired_date' => $this->expired_date,
                'content' => $this->content
            ],
            'signatures' => RequestSignatureResource::collection($this->requestSignatures),
            'document' => new DocumentResource($this->documents()->isShow()->first()),
            'canvas' => [
                'width' => 0,
                'height' => 0,
            ]
        ];
    }
}
