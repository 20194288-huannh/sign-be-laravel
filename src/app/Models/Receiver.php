<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Receiver extends Model
{
    use HasFactory;
    const TYPE_SIGNER = 0;
    const TYPE_CC = 1;

    protected $guarded = [];

    /**
     * Get all of the post's comments.
     */
    public function actions(): MorphMany
    {
        return $this->morphMany(Action::class, 'actionable');
    }
}
