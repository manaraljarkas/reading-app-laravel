<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReaderChallenge extends Model
{
    use SoftDeletes;
    protected $fillable = ['progress', 'percentage', 'reader_id', 'challenge_id'];
}
