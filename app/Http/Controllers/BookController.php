<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Comment;
use App\Models\Reader;
use App\Models\User;
use App\Helpers\CountryHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function getBookFile($BookId)
    {
        $reader = Auth::user();
        $book = Book::select('book_pdf')->where('id', '=', $BookId)->first();

        $fileUrl = asset('storage/images/books/pdfs/' . $book->book_pdf);

        return response()->json(['pdf_url' => $fileUrl]);
    }

    public function getBooksComments($BookId)
    {
        $reader = Auth::user();
        $comments = Comment::where('book_id', '=', $BookId)->get();

        return response()->json(
            $comments
        );
    }

    public function getNumbers()
    {
        $reader = Auth::user();

        $readers = Reader::Count();
        $books = Book::Count();
        $challenges = Challenge::Count();
        $categories = Category::Count();
        $admins = User::where('role', '=', 'admin')->count();

        return response()->json([
            'number_of_readers' => $readers,
            'number_of_books' => $books,
            'number_of_challenges' => $challenges,
            'number_of_categories' => $categories,
            'number_of_admins' => $admins
        ]);
    }

    public function getMostRatedBooks()
    {
        $user_id = Auth::id();

        $books = Book::with([
            'author.country',
            'category',
            'sizecategory'
        ])
            ->withCount(['readers as readers_count'])
            ->addSelect([
                'star_rate' => function ($query) {
                    $query->from('reader_books')
                        ->selectRaw('AVG(rating)')
                        ->whereColumn('reader_books.book_id', 'books.id');
                }
            ])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('reader_books')
                    ->whereColumn('reader_books.book_id', 'books.id');
            })
            ->orderByDesc('star_rate')
            ->take(10)
            ->get()
            ->map(function ($book) use ($user_id) {
                $author = $book->author;
                $country = $author?->country;
                $countryFlag = $country?->code ? CountryHelper::countryToEmoji($country->code) : null;

                $readerBook = DB::table('reader_books')
                    ->where('book_id', $book->id)
                    ->where('reader_id', $user_id)
                    ->first();

                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'description' => $book->description,
                    'author_name' => $author?->name,
                    'country_flag' => $countryFlag,
                    'publish_date' => $book->publish_date,
                    'cover_image' => $book->cover_image,
                    'star_rate' => round($book->star_rate),
                    'readers_count' => $book->readers_count,
                    'category_name' => $book->category?->name,
                    'size_category_name' => $book->sizecategory?->name,
                    'number_of_pages' => $book->number_of_pages,
                    'is_favourite' => (bool) ($readerBook->is_favourite ?? false),
                    'is_in_library' => (bool) $readerBook,
                ];
            });

        return response()->json($books);
    }

    public function getAuthorBooks($authorId)
    {
        $user_id = Auth::id();

        $books = Book::with([
            'author.country',
            'category',
            'sizecategory'
        ])
            ->where('author_id', $authorId)
            ->withCount(['readers as readers_count'])
            ->addSelect([
                'star_rate' => function ($query) {
                    $query->from('reader_books')
                        ->selectRaw('AVG(rating)')
                        ->whereColumn('reader_books.book_id', 'books.id');
                }
            ])
            ->get()
            ->map(function ($book) use ($user_id) {
                $author = $book->author;
                $country = $author?->country;
                $countryFlag = $country?->code ? CountryHelper::countryToEmoji($country->code) : null;

                $readerBook = DB::table('reader_books')
                    ->where('book_id', $book->id)
                    ->where('reader_id', $user_id)
                    ->first();

                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'description' => $book->description,
                    'author_name' => $author?->name,
                    'country_flag' => $countryFlag,
                    'publish_date' => $book->publish_date,
                    'cover_image' => $book->cover_image,
                    'star_rate' => round($book->star_rate),
                    'readers_count' => $book->readers_count,
                    'category_name' => $book->category?->name,
                    'size_category_name' => $book->sizecategory?->name,
                    'number_of_pages' => $book->number_of_pages,
                    'is_favourite' => (bool) ($readerBook->is_favourite ?? false),
                    'is_in_library' => (bool) $readerBook,
                ];
            });

        return response()->json($books);
    }

    public function getCategoryBooks($categoryId)
    {
        $user_id = Auth::id();

        $books = Book::with([
            'author.country',
            'category',
            'sizecategory'
        ])
            ->where('category_id', $categoryId)
            ->withCount(['readers as readers_count'])
            ->addSelect([
                'star_rate' => function ($query) {
                    $query->from('reader_books')
                        ->selectRaw('AVG(rating)')
                        ->whereColumn('reader_books.book_id', 'books.id');
                }
            ])
            ->get()
            ->map(function ($book) use ($user_id) {
                $author = $book->author;
                $country = $author?->country;
                $countryFlag = $country?->code ? CountryHelper::countryToEmoji($country->code) : null;

                $readerBook = DB::table('reader_books')
                    ->where('book_id', $book->id)
                    ->where('reader_id', $user_id)
                    ->first();

                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'description' => $book->description,
                    'author_name' => $author?->name,
                    'country_flag' => $countryFlag,
                    'publish_date' => $book->publish_date,
                    'cover_image' => $book->cover_image,
                    'star_rate' => round($book->star_rate),
                    'readers_count' => $book->readers_count,
                    'category_name' => $book->category?->name,
                    'size_category_name' => $book->sizecategory?->name,
                    'number_of_pages' => $book->number_of_pages,
                    'is_favourite' => (bool) ($readerBook->is_favourite ?? false),
                    'is_in_library' => (bool) $readerBook,
                ];
            });

        return response()->json($books);
    }
}
