<?php

namespace App\Http\Controllers;

use App\Helpers\CountryHelper;
use App\Models\Book;
use App\Models\BookChallenge;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Comment;
use App\Models\Reader;
use App\Models\ReaderBook;
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
        $fileUrl = asset('storage/images/books/pdfs/' . $book->book_pdf);

        return response()->json(['pdf_url' => $fileUrl]);
        return response()->json(['pdf_url' => $fileUrl]);
    }

    public function getBooksComments($BookId)
    {
        $reader = Auth::user();
        $comments = Comment::where('book_id', '=', $BookId)->with('reader')->with('reader')->get();

        $comments = $comments->map(function ($comment) {
            return [
                'reader_id' => $comment->reader_id,
                'reader_name' => $comment->reader?->first_name,
                'reader_image' => asset('storage/' . $comment->reader?->image),
                'comment' => $comment->comment,
            ];
        });
        return response()->json($comments);
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
                    'title' => $book->title,
                    'author_name' => $book->author?->name,
                    'category' => $book->category?->name,
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
                    'image' => asset('storage/' . $comment->reader?->image),
                    'name' => $comment->reader?->first_name,
                    'comment' => $comment->comment,
                ];
            });

        return response()->json([
            'size_Category' => $book->sizecategory->name,
            'description' => $book->description,
            'summary' => $book->summary,
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

    public function store(Request $request)
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
                $locale = app()->getLocale();

                $author = $book->author;
                $country = $author?->country;
                $countryFlag = $country?->code ? CountryHelper::countryToEmoji($country->code) : null;

                $readerBook = DB::table('reader_books')
                    ->where('book_id', $book->id)
                    ->where('reader_id', $user_id)
                    ->first();

                return [
                    'id' => $book->id,
                    'title' => $book->getTranslation('title', $locale),
                    'description' => $book->getTranslation('description', $locale),
                    'author_name' => $author?->getTranslation('name', $locale),
                    'country_flag' => $countryFlag,
                    'publish_date' => $book->publish_date,
                    'cover_image' => $book->cover_image,
                    'star_rate' => round($book->star_rate),
                    'readers_count' => $book->readers_count,
                    'category_name' => $book->category?->getTranslation('name', $locale),
                    'size_category_name' => $book->sizecategory?->getTranslation('name', $locale),
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
                $locale = app()->getLocale();
                $author = $book->author;
                $country = $author?->country;
                $countryFlag = $country?->code ? CountryHelper::countryToEmoji($country->code) : null;

                $readerBook = DB::table('reader_books')
                    ->where('book_id', $book->id)
                    ->where('reader_id', $user_id)
                    ->first();

                return [
                    'id' => $book->id,
                    'title' => $book->getTranslation('title', $locale),
                    'description' => $book->getTranslation('description', $locale),
                    'author_name' => $author?->getTranslation('name', $locale),
                    'country_flag' => $countryFlag,
                    'publish_date' => $book->publish_date,
                    'cover_image' => $book->cover_image,
                    'star_rate' => round($book->star_rate),
                    'readers_count' => $book->readers_count,
                    'category_name' => $book->category?->getTranslation('name', $locale),
                    'size_category_name' => $book->sizecategory?->getTranslation('name', $locale),
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

    public function getBookComments($bookId)
    {
        $user = Auth::id();
        $comments = Comment::with('reader')->where('book_id', '=', $bookId)->get()->map(function ($comment) use ($bookId) {
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
        $reader = Auth::user()->reader;

        if (!$reader) {
            return response()->json(['message' => 'Reader not found for this user.'], 404);
        }

        $validated = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment = Comment::create([
            'comment' => $request->comment,
            'book_id' => $bookId,
            'reader_id' => $reader->id,
        ]);
    }

    public function AddBookToFavorite($bookId)
    {
        $reader = Auth::user()->reader;

        if (!$reader) {
            return response()->json(['message' => 'Reader not found.'], 404);
        }

        $book = Book::findOrFail($bookId);

        $reader->books()->syncWithoutDetaching([
            $bookId => ['is_favourite' => true]
        ]);
    }

    public function AddBookToDoList($bookId)
    {
        $reader = Auth::user()->reader;
        if (!$reader) {
            return response()->json(['message' => 'Reader not found.'], 404);
        }
        $book = Book::findOrFail($bookId);
        $reader->books()->syncWithoutDetaching([
            $bookId => ['status' => 'to_read']
        ]);
    }

    public function RateBook($bookId, Request $request)
    {
        $reader = Auth::user()->reader;
        if (!$reader) {
            return response()->json(['message' => 'Reader not found.'], 404);
        }
        $validated = $request->validate([
            'rate' => 'integer|required|min:1|max:5'
        ]);
        $book = Book::findOrFail($bookId);
        $reader->books()->syncWithoutDetaching([
            $bookId => ['rating' => $request->rate]
        ]);
    }
}
