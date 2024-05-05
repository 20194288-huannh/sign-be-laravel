<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestSignature extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Receiver::class);
    }
}
