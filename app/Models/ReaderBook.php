<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReaderBook extends Model
{
    protected $fillable = ['progress','is_favourite','status', 'book_id', 'reader_id'];
}
