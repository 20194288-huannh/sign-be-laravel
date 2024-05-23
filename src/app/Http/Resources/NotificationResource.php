<?php

namespace App\Http\Resources;

use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'receiver' => new ReceiverResource($this->receiver),
            'document' => new DocumentResource($this->document),
            'content' => $this->content,
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y h:i:s A')
        ];
    }
}
