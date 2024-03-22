<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Signature extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
