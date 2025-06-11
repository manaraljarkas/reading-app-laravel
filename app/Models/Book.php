<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'description',
        'title',
        'book_pdf',
        'publish_date',
        'star_rate',
        'cover_image',
        'number_of_pages',
        'category_id',
        'author_id',
        'category_size_id',
        'summary'
    ];
        public function comments()
    {
        return $this->hasMany(Comment::class);
    }
        public function authors()
    {
        return $this->belongsTo(Author::class);
    }
        public function readers()
    {
        return $this->belongsToMany(Reader::class, 'reader_books');
    }
        public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'challenge_books');
    }
        public function sizecategories()
    {
        return $this->belongsTo(SizeCategory::class);
    }
        public function categories()
    {
        return $this->belongsTo(Category::class);
    }

}
