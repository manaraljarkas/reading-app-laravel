<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class BookChallenge extends Model
{
    use SoftDeletes,HasTranslations;
    protected $fillable = ['duration', 'points', 'description', 'book_id'];
    protected $casts = [
    'description' => 'array',
];
    protected $translatable = ['description'];
}
