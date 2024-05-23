<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Action;

class Document extends Model
{
    use HasFactory;
    protected $guarded = [];

    const STATUS_COMPLETED = 0;
    const STATUS_DRAFT = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_NEED_REVIEWS = 4;
    const STATUS_SENT = 5;

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

    public function scopeIsShow($query)
    {
        return $query->where('is_show', 1);
    }

    public function signatures(): BelongsToMany
    {
        return $this->belongsToMany(Signature::class, 'document_signatures');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
}
