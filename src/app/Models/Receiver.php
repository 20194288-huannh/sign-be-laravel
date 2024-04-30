<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiver extends Model
{
    use HasFactory;
    const TYPE_SIGNER = 0;
    const TYPE_CC = 1;

    protected $guarded = [];
}
