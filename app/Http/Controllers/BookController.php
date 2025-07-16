<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\BookChallenge;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Comment;
use App\Models\Reader;
use App\Models\ReaderBook;
use App\Models\User;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{

    protected BookService $service;

    public function __construct(BookService $service)
    {
        $this->service = $service;
    }

    public function getBookFile($BookId)
    {
        $user = Auth::user();
        $reader = $user->reader;

        if (!$reader) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }

        $book = Book::select('book_pdf')->where('id', $BookId)->first();
        if (!$book || !$book->book_pdf) {
            return response()->json(['message' => 'Book file not found.'], 404);
        }

        $readerBook = ReaderBook::where('reader_id', $reader->id)
            ->where('book_id', $BookId)
            ->first();

        $currentPage = ($readerBook && $readerBook->progress > 0) ? $readerBook->progress : 1;

        $fileUrl = asset('storage/images/books/pdfs/' . $book->book_pdf);

        return response()->json([
            'pdf_url' => $fileUrl,
            'current_page' => $currentPage
        ]);
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

    public function index()
    {
        $user = Auth::user();
        $rate = DB::table('reader_books');
        $books = Book::with('category', 'author')
            ->withcount('readers')
            ->withAvg('readers as average_rating', 'reader_books.rating')
            ->paginate(10)
            ->through(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->getTranslations('title'),
                    'author_name' => $book->author?->getTranslations('name'),
                    'category' => $book->category?->getTranslations('name'),
                    'publish_date' => $book->publish_date,
                    'star_rate' => round($book->average_rating, 2),
                    'number_of_readers' => $book->readers_count,
                ];
            });

        return response()->json($books);
    }

    public function show($bookId)
    {
        $user = Auth::user();
        $book = Book::where('id', '=', $bookId)->with('sizecategory')->with('bookChallenges')->first();
        $number_of_participants = DB::table('reader_books')->where('reader_books.book_id', '=', $bookId)->count();

        $comments = Comment::where('book_id', '=', $bookId)
            ->with('reader')
            ->get()
            ->map(function ($comment) {
                return [
                    'image' => $comment->reader?->image
                        ? asset('storage/images/readers/' . $comment->reader->image)
                        : null,
                    'name' => $comment->reader?->first_name,
                    'comment' => $comment->comment,
                ];
            });

        return response()->json([
            'size_Category' => $book->sizecategory->getTranslations('name'),
            'description' => $book->getTranslations('description'),
            'summary' => $book->getTranslations('summary'),
            'book_challenge_duration' => $book->bookChallenges?->duration,
            'book_challenge_points' => $book->bookChallenges?->points,
            'book_challenge_participants' => $number_of_participants,
            'comments' => $comments,
        ]);
    }

    public function destroy($bookId)
    {
        $user = Auth::user();
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        $book->delete();
        return response()->json(['message' => 'book deleted successufly']);
    }

    public function store(StoreBookRequest $request)
    {
        $user = Auth::user();
        DB::transaction(function () use ($request) {

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
                 'summary' => [
                    'en' => $request->input('summary')['en'],
                    'ar' => $request->input('summary')['ar'],
                ],
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

    public function getMostRatedBooks(): JsonResponse
    {
        $books = $this->service->getTopRatedBooks();
        return $this->respond($books);
    }

    public function getAuthorBooks($authorId): JsonResponse
    {
        $books = $this->service->getBooksByAuthor($authorId);
        return $this->respond($books);
    }

    public function getCategoryBooks($categoryId): JsonResponse
    {
        $books = $this->service->getBooksByCategory($categoryId);
        return $this->respond($books);
    }

    public function getFavoriteBooks(): JsonResponse
    {
        $books = $this->service->getFavoriteBooks();
        return $this->respond($books);
    }

    public function getToReadBooks(): JsonResponse
    {
        $books = $this->service->getBooksByStatus('to_read');
        return $this->respond($books);
    }

    public function getInReadBooks(): JsonResponse
    {
        $books = $this->service->getBooksByStatus('in_read');
        return $this->respond($books);
    }

    public function getCompletedBooks(): JsonResponse
    {
        $books = $this->service->getBooksByStatus('completed');
        return $this->respond($books);
    }

    private function respond($books)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->transformBooks($books),
        ]);
    }

    public function getBookComments($bookId)
    {
        $user = Auth::user();
        $comments = Comment::with('reader')->where('book_id', '=', $bookId)->get()
            ->map(function ($comment) use ($bookId) {
                return [
                    'reader_name' => $comment->reader?->first_name,
                    'reader_image' => $comment->reader?->picture ? asset('storage/images/readers/' . $comment->reader->picture)
                        : null,
                    'reader_nickname' => $comment->reader?->nickname,
                    'comment' => $comment->comment
                ];
            });
        return response()->json($comments);
    }

    public function AddCommentToTheBook($bookId, Request $request)
    {
        $reader = Auth::user();
        $book = Book::find($bookId);
        if (!$book) {
            return response()->json(['message' => 'Book not found .'], 404);
        }

        $validated = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment = Comment::create([
            'comment' => $request->comment,
            'book_id' => $bookId,
            'reader_id' => $reader->id,
        ]);
        return response()->json([
            'message' => 'comment added successfully'
        ]);
    }

    public function AddBookToFavorite($bookId)
    {
        $user = Auth::user();
        $reader = $user->reader;

        if (!$reader) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }
        $book = Book::findOrFail($bookId);
        if (!$book) {
            return response()->json(['message' => 'Book not found.'], 404);
        }
        $reader->books()->syncWithoutDetaching([
            $bookId => ['is_favourite' => true]
        ]);
        return response()->json(['message' => 'Book added to favorite successufly']);
    }

    public function AddBookToDoList($bookId)
    {
        $user = Auth::user();
        $reader = $user->reader;
        if (!$reader) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }
        $book = Book::findOrFail($bookId);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }
        $reader->books()->syncWithoutDetaching([
            $bookId => ['status' => 'to_read']
        ]);
        return response()->json(['message' => 'Book added to To-Do List']);
    }

    public function RateBook($bookId, Request $request)
    {
        $user = Auth::user();
        $reader = $user->reader;

        $book = Book::findOrFail($bookId);
        if (!$reader) {
            return response()->json(['message' => 'Reader not found.'], 404);
        }
        if (!$book) {
            return response()->json(['message' => 'Book not Found']);
        }
        $validated = $request->validate([
            'rate' => 'integer|required|min:1|max:5'
        ]);

        $reader->books()->syncWithoutDetaching([
            $bookId => ['rating' => $request->rate]
        ]);
        return response()->json(['message' => 'Book rated successfully']);
    }
}
