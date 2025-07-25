<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Book extends Model
{
    use SoftDeletes, HasTranslations;
    public $translatable = ['title', 'description','summary'];
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
        return $this->belongsToMany(User::class, 'reader_books', 'book_id', 'reader_id')
            ->withPivot('is_favourite', 'rating');
    }
    public function readerBooks()
    {
        return $this->hasMany(ReaderBook::class, 'book_id');
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
