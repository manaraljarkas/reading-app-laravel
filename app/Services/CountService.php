<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CountService
{
    protected $readerId;


    public function __construct($readerId)
    {
        $this->readerId = $readerId;
    }

    public function countChallenges()
    {
        return DB::table('reader_challenges')
            ->where('reader_id', $this->readerId)
            ->count();
    }

    public function countBooks()
    {
        return DB::table('reader_books')
            ->where('reader_id', $this->readerId)
            ->count();
    }

    public function countCountries()
    {
        return DB::table('reader_books')
            ->join('books', 'reader_books.book_id', '=', 'books.id')
            ->join('authors', 'authors.id', '=', 'books.author_id')
            ->where('reader_books.reader_id', $this->readerId)
            ->distinct('authors.country_id')
            ->count('authors.country_id');
    }
    public function Number_of_books_in_favorites()
    {
        return DB::table('reader_books')->where('reader_id', '=', $this->readerId)->where('is_favourite',1)->count();
    }
    public function countBadges()
    {
        return DB::table('reader_badges')
            ->where('reader_id', $this->readerId)
            ->count();
    }
}
