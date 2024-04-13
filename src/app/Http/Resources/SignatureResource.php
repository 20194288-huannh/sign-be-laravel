<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SignatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => $this->user,
            'sha256_original_file' => $this->sha256_original_file,
            'priority' => $this->priority,
            'type' => $this->type,
            'file' => $this->file
        ];
    }
}
