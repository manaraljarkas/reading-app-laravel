<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['commnet','book_id','reader_id'];
    public function readers()
    {
        return $this->belongsTo(Reader::class);
    }
    public function books()
    {
        return $this->belongsTo(Book::class);
    }
}
