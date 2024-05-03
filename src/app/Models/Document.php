<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory;
    protected $guarded = [];

    const STATUS_NEED_REVIEWS = 0;
    const STATUS_DRAFT = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_COMPLETED = 3;
    const STATUS_SENT = 4;
    const STATUS_EXPIRED = 4;

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    /**
     * Scope a query to get document by user id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByUserId($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeHasStatus($query, array $status)
    {
        return $query->whereIn('status', $status);
    }

    public function signatures(): BelongsToMany
    {
        return $this->belongsToMany(Signature::class, 'document_signatures');
    }
}
