<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CountryHelper;

class BookService
{
    public function getTopRatedBooks(int $limit = 10)
    {
        return $this->baseQuery()
            ->whereHas('readers')
            ->orderByDesc('star_rate')
            ->take($limit)
            ->get();
    }

    public function getBooksByAuthor($authorId)
    {
        return $this->baseQuery()
            ->where('author_id', $authorId)
            ->get();
    }

    public function getBooksByCategory($categoryId)
    {
        return $this->baseQuery()
            ->where('category_id', $categoryId)
            ->get();
    }

    public function getBooksByStatus(string $status)
    {
        $readerId = $this->getReaderId();
        if (!$readerId) return collect();

        return $this->baseQuery()
            ->whereHas('readerBooks', function ($query) use ($readerId, $status) {
                $query->where('reader_id', $readerId)
                    ->where('status', $status);
            })
            ->orderByDesc('star_rate')
            ->get();
    }

    public function getFavoriteBooks()
    {
        $readerId = $this->getReaderId();
        if (!$readerId) return collect();

        return $this->baseQuery()
            ->whereHas('readerBooks', function ($query) use ($readerId) {
                $query->where('reader_id', $readerId)
                    ->where('is_favourite', true);
            })
            ->orderByDesc('star_rate')
            ->get();
    }

    public function transformBooks($books)
    {
        $readerId = $this->getReaderId();
        $locale = app()->getLocale();

        return $books->map(function ($book) use ($readerId, $locale) {
            $readerBook = $book->readerBooks->firstWhere('reader_id', $readerId);

            return [
                'id' => $book->id,
                'title' => $book->getTranslation('title', $locale),
                'description' => $book->getTranslation('description', $locale),
                'author_name' => optional($book->author)->getTranslation('name', $locale),
                'country_flag' => $book->author && $book->author->country
                    ? CountryHelper::countryToEmoji($book->author->country->code)
                    : null,
                'publish_date' => $book->publish_date,
                'cover_image' => $book->cover_image,
                'star_rate' => round($book->star_rate),
                'readers_count' => $book->readers_count,
                'category_name' => optional($book->category)->getTranslation('name', $locale),
                'size_category_name' => optional($book->sizecategory)->getTranslation('name', $locale),
                'number_of_pages' => $book->number_of_pages,
                'is_favourite' => (bool) optional($readerBook)->is_favourite,
                'is_in_library' => !is_null($readerBook),
            ];
        });
    }

    private function baseQuery()
    {
        return Book::with([
            'author.country',
            'category',
            'sizecategory',
            'readerBooks' => function ($q) {
                $q->where('reader_id', $this->getReaderId());
            }
        ])
            ->select('books.*')
            ->addSelect([
                // Average star rating across all readers
                'star_rate' => function ($query) {
                    $query->from('reader_books')
                        ->selectRaw('AVG(rating)')
                        ->whereColumn('reader_books.book_id', 'books.id');
                },
                // Count only readers with progress > 0
                'readers_count' => function ($query) {
                    $query->from('reader_books')
                        ->selectRaw('COUNT(*)')
                        ->whereColumn('reader_books.book_id', 'books.id')
                        ->where('progress', '>', 0);
                }
            ]);
    }

    private function getReaderId(): ?int
    {
        return Auth::user()?->reader?->id;
    }
}
