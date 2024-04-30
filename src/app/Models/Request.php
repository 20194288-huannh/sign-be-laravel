<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Request extends Model
{
    use HasFactory;
    const TYPE_REQUEST = 1;
    const TYPE_REPLY = 2;

    protected $guarded = [];

    public function receivers(): HasMany
    {
        return $this->hasMany(Receiver::class);
    }

    public function requestSignatures(): HasMany
    {
        return $this->hasMany(RequestSignature::class, 'request_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
