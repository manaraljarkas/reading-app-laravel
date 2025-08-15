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
            ->whereHas('readerBooks', function ($q) {
                $q->whereNotNull('rating')->where('rating', '>', 0);
            })
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
                'points' => $book->points,
                'star_rate' => round($book->star_rate),
                'readers_count' => $book->readers_count,
                'category_name' => optional($book->category)->getTranslation('name', $locale),
                'size_category_name' => optional($book->sizecategory)->getTranslation('name', $locale),
                'number_of_pages' => $book->number_of_pages,
                'progress' => optional($readerBook)->progress ?? 0,
                'is_favourite' => (bool) optional($readerBook)->is_favourite,
                'is_challenged' => (bool) optional($readerBook)->is_challenged,
                'is_in_library' => !is_null($readerBook),
            ];
        });
    }

    private function baseQuery()
    {
        $readerId = $this->getReaderId();

        return Book::with([
            'author.country',
            'category',
            'sizecategory',
            'readerBooks' => function ($q) use ($readerId) {
                if ($readerId) {
                    $q->where('reader_id', $readerId);
                }
            }
        ])
            ->select('books.*')
            ->addSelect([
                'star_rate' => function ($query) {
                    $query->from('reader_books')
                        ->selectRaw('COALESCE(AVG(rating), 0)')
                        ->whereColumn('reader_books.book_id', 'books.id');
                },
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
