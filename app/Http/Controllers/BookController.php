<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookChallenge;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Comment;
use App\Models\Reader;
use App\Models\User;
use App\Helpers\CountryHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function getBookFile($BookId)
    {
        $reader = Auth::user();
        $book = Book::select('book_pdf')->where('id', '=', $BookId)->first();
    public function getBookFile($BookId)
    {
        $reader = Auth::user();
        $book = Book::select('book_pdf')->where('id', '=', $BookId)->first();

        $fileUrl = asset('storage/images/books/pdfs/' . $book->book_pdf);
        $fileUrl = asset('storage/images/books/pdfs/' . $book->book_pdf);

        return response()->json(['pdf_url' => $fileUrl]);
        return response()->json(['pdf_url' => $fileUrl]);
    }

    public function getBooksComments($BookId)
    {
        $reader = Auth::user();
        $comments = Comment::where('book_id', '=', $BookId)->with('reader')->get();

        $comments = $comments->map(function ($comment) {
            return [
                'reader_id' => $comment->reader_id,
                'reader_name' => $comment->reader?->first_name,
                'reader_image' => asset('storage/' . $comment->reader?->image),
                'comment' => $comment->comment,
            ];
        });
        return response()->json($comments);
    }

    public function getNumbers()
    {
        $reader = Auth::user();

        $readers = Reader::Count();
        $books = Book::Count();
        $challenges = Challenge::Count();
        $categories = Category::Count();
        $admins = User::where('role', '=', 'admin')->count();
    public function getBooksComments($BookId)
    {
        $reader = Auth::user();
        $comments = Comment::where('book_id', '=', $BookId)->get();

        return response()->json([
            'number_of_readers' => $readers,
            'number_of_admins' => $admins,
            'number_of_books' => $books,
            'number_of_challenges' => $challenges,
            'number_of_categories' => $categories,
        ]);
        return response()->json(
            $comments
        );
    }

    public function getBooks()
    {
        $user = Auth::user();
        $books = Book::with('category', 'author')
            ->withcount('readers')
            ->paginate(10)
            ->through(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author_name' => $book->author?->name,
                    'category' => $book->category?->name,
                    'publish_date' => $book->publish_date,
                    'star_rate' => $book->star_rate,
                    'number_of_readers' => $book->readers_count,
                ];
            });

        return response()->json($books);
    }

    public function getBookInfo($bookId)
    {
        $user = Auth::user();
        $book = Book::where('id', '=', $bookId)->with('sizecategory')->with('bookChallenges')->first();
        $number_of_participants = DB::table('reader_books')->where('reader_books.book_id', '=', $bookId)->count();

        $comments = Comment::where('book_id', '=', $bookId)
            ->with('reader')
            ->get()
            ->map(function ($comment) {
                return [
                    'image' => asset('storage/' . $comment->reader?->image),
                    'name' => $comment->reader?->first_name,
                    'comment' => $comment->comment,
                ];
            });
    public function getNumbers()
    {
        $reader = Auth::user();

        $readers = Reader::Count();
        $books = Book::Count();
        $challenges = Challenge::Count();
        $categories = Category::Count();
        $admins = User::where('role', '=', 'admin')->count();

        return response()->json([
jlhl}
