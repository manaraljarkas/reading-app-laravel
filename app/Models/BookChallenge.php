<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookChallenge extends Model
{
    use SoftDeletes;
    protected $fillable = ['duration', 'points', 'description', 'book_id'];
    protected $casts = [
    'description' => 'array',
];
}
