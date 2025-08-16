<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookSuggestion extends Model
{
    use SoftDeletes;
    protected $fillable = ['title', 'author_name', 'note', 'reader_id','status'];

    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }
}
