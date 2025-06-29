<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookChallenge;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Comment;
use App\Models\Reader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return response()->json([
            'number_of_readers' => $readers,
            'number_of_admins' => $admins,
            'number_of_books' => $books,
            'number_of_challenges' => $challenges,
            'number_of_categories' => $categories,
        ]);
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

        return response()->json([
            'size_Category' => $book->sizecategory->name,
            'description' => $book->description,
            'summary' => $book->summary,
            'book_challenge_duration' => $book->bookChallenges->duration,
            'book_challenge_points' => $book->bookChallenges->points,
            'book_challenge_participants' => $number_of_participants,
            'comments' => $comments,
        ]);
    }
    public function deleteBook($bookId)
    {
        $user = Auth::user();
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        $book->delete();
        return response()->json(['message' => 'book deleted successufly']);
    }
    public function addBook(Request $request)
    {
        $user = Auth::user();
        DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'title.en' => 'required|string',
                'title.ar' => 'required|string',
                'author_id' => 'required|integer|exists:authors,id',
                'description.en' => 'required|string',
                'description.ar' => 'required|string',
                'category_id' => 'required|integer|exists:categories,id',
                'publish_date' => 'required|date',
                'number_of_pages' => 'integer|required',
                'size_category_id' => 'integer|required|exists:size_categories,id',
                'summary' => 'sometimes|string',
                'book_file' => 'required|file',
                'cover_image' => 'required|image',
                'challenge_duration' => 'required|integer',
                'challenge_points' => 'required|integer',
                'description_BookChallenge.en' => 'required|string',
                'description_BookChallenge.ar' => 'required|string',
            ]);
            $filepath = $request->file('book_file')->store('storage/books/pdfs', 'public');
            $coverpath = $request->file('cover_image')->store('storage/books/covers', 'public');
            $book = Book::create([
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar'],
                ],
                'description' => [
                    'en' => $request->input('description')['en'],
                    'ar' => $request->input('description')['ar'],
                ],
                'author_id' => $request->author_id,
                'publish_date' => $request->publish_date,
                'number_of_pages' => $request->number_of_pages,
                'summary' => $request->summary,
                'book_pdf' => $filepath,
                'cover_image' => $coverpath,
                'size_category_id' => $request->size_category_id,
                'category_id' => $request->category_id,
            ]);

            BookChallenge::create([
                'duration' => $request->challenge_duration,
                'points' => $request->challenge_points,
                'description' => [
                    'en' => $request->input('description_BookChallenge')['en'],
                    'ar' => $request->input('description_BookChallenge')['ar'],
                ],
                'book_id' => $book->id,
            ]);
        });
    }
  
}
