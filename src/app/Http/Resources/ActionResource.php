<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->actionable->name,
            'email' => $this->actionable->email,
            'content' => $this->content,
            'document' => $this->document,
            'created_at' => Carbon::parse($this->created_at)->format('M d, Y h:i:s A')
        ];
    }
}
