<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;
    protected $guarded = [];

    const STATUS_COMPLETED = 0;
    const STATUS_DRAFT = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_NEED_REVIEWS = 4;
    const STATUS_SENT = 5;

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Receiver::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
