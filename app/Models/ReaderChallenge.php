<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReaderChallenge extends Model
{
    protected $fillable = ['progress', 'reader_id', 'challenge_id'];
}
