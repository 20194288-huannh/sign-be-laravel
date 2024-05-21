<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            'sha256' => $this->sha256,
            'status' => $this->status,
            'user' => new UserResource($this->user),
            'receiver' => $this->receiver,
            'file' => new FileResource($this->file),
            'requested_on' => Carbon::parse($this->created_at)->format('M d, Y h:i:s A')
        ];
    }
}
