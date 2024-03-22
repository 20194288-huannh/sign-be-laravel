<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function imageable(): MorphTo
    {
        return $this->morphTo();
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
}
