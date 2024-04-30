<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestSignatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => [
                'top' => $this->top,
                'left' => $this->left,
                'width' => $this->width,
                'height' => $this->height,
            ],
            'page' => $this->page,
            'can_resize' => false
        ];
    }
}
