<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use App\Models\SizeCategory;

class SearchService
{
    protected string $locale;

    public function __construct()
    {
        $this->locale = app()->getLocale();
    }

    public function search(?string $keyword = null): array
    {
        $books = Book::with(['author.country', 'category', 'sizeCategory'])
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($q) use ($keyword) {
                    $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) LIKE ?", ["%{$keyword}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ar')) LIKE ?", ["%{$keyword}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.en')) LIKE ?", ["%{$keyword}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.ar')) LIKE ?", ["%{$keyword}%"])
                        ->orWhereDate('publish_date', $keyword);
                });
            })
            ->get();

        $authors = Author::with('country')
            ->when($keyword, function ($q) use ($keyword) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$keyword}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$keyword}%"]);
            })
            ->get();

        $categories = Category::when($keyword, function ($q) use ($keyword) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$keyword}%"])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$keyword}%"]);
        })
            ->get();

        $sizeCategories = SizeCategory::when($keyword, function ($q) use ($keyword) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$keyword}%"])
                ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$keyword}%"]);
        })
            ->get();

        return [
            'books' => $this->transformBooks($books),
            'authors' => $this->transformAuthors($authors),
            'categories' => $this->transformCategories($categories),
            'size_categories' => $this->transformSizeCategories($sizeCategories),
        ];
    }

    protected function transformBooks($books)
    {
        return $books->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->getTranslation('title', $this->locale),
                'description' => $book->getTranslation('description', $this->locale),
                'publish_date' => $book->publish_date,
                'cover_image' => $book->cover_image,
                'number_of_pages' => $book->number_of_pages,
                'summary' => $book->summary ? $book->getTranslation('summary', $this->locale) : null,
                'author_name' => optional($book->author)->getTranslation('name', $this->locale),
                'category_name' => optional($book->category)->getTranslation('name', $this->locale),
                'size_category_name' => optional($book->sizeCategory)->getTranslation('name', $this->locale),
            ];
        });
    }

    protected function transformAuthors($authors)
    {
        return $authors->map(function ($author) {
            return [
                'id' => $author->id,
                'name' => $author->getTranslation('name', $this->locale),
                'image' => $author->image,
            ];
        });
    }

    protected function transformCategories($categories)
    {
        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->getTranslation('name', $this->locale),
                'icon' => $category->icon,
            ];
        });
    }

    protected function transformSizeCategories($sizeCategories)
    {
        return $sizeCategories->map(function ($sizeCategory) {
            return [
                'id' => $sizeCategory->id,
                'name' => $sizeCategory->getTranslation('name', $this->locale),
            ];
        });
    }
}
