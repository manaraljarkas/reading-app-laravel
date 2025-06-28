<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReaderBook extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'progress',
        'status',
        'is_favourite',
        'is_challenged',
        'book_id',
        'reader_id',
        'rating'
    ];
}
