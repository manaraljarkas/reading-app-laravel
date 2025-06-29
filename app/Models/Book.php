<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;
        protected $casts = [
    'title' => 'array',
    'description' => 'array',
    'summary' => 'array',
       ];
    protected $fillable = [
        'title',
        'description',
        'publish_date',
        'book_pdf',
        'cover_image',
        'star_rate',
        'number_of_pages',
        'summary',
        'category_id',
        'author_id',
        'size_category_id',
    ];


        public function comments()
    {
        return $this->hasMany(Comment::class);
    }
        public function author()
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
        public function sizecategory()
    {
        return $this->belongsTo(SizeCategory::class, 'size_category_id');
    }
        public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function bookChallenges()
    {
        return $this->hasOne(bookChallenge::class);
    }
    public function bookSuggestions()
    {
        return $this->hasMany(BookSuggestion::class);
    }
}
