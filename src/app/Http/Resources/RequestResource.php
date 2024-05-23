<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'content' => $this->content,
            'created_at' => $this->created_at,
            'expired_date' => $this->expired_date,
            'status' => $this->status,
            'title' => $this->title,
            'type' => $this->type,
            'receivers' => ReceiverResource::collection($this->receivers),
        ];
    }
}
