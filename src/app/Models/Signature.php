<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Signature extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to get signature by user id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByUserId($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
