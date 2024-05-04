<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Action extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeGetByDocuments($query, $documentIds)
    {
        return $query->whereIn('document_id', $documentIds);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
