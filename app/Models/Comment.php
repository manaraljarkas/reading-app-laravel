<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;
    protected $fillable = ['commnet','book_id','reader_id'];
    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }
    public function books()
    {
        return $this->belongsTo(Book::class);
    }
}
