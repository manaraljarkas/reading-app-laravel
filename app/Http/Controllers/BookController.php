<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Badge;
use App\Models\Book;
use App\Models\ReaderBook;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Comment;
use App\Models\Reader;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\BookService;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewBookNotification;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    protected BookService $service;
    protected $fcmService;

    public function __construct(BookService $service, FcmService $fcmService)
    {
        $this->service = $service;
        $this->fcmService = $fcmService;
    }
    public function getBookFile($bookId)
    {
        $user = Auth::user();
        $reader = $user->reader;

        if (!$reader) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }

        $book = Book::select('id', 'book_pdf')->find($bookId);

        if (!$book || !$book->book_pdf) {
            return response()->json(['message' => 'Book file not found.'], 404);
        }

        return response()->json([
            'pdf_url' => $book->book_pdf,
        ]);
    }

    public function getNumbers()
    {
        $reader = Auth::user();

        $readers = Reader::Count();
        $books = Book::Count();
        $challenges = Challenge::Count();
        $categories = Category::Count();
        $badges = Badge::Count();
        $admins = User::where('role', '=', 'admin')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'number_of_readers' => $readers,
                'number_of_admins' => $admins,
                'number_of_books' => $books,
                'number_of_challenges' => $challenges,
                'number_of_categories' => $categories,
                'number_of_badges' => $badges
            ]
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $rate = DB::table('reader_books');
        $books = Book::with('category', 'author')
            ->withcount('readers')
            ->withAvg('readers as average_rating', 'reader_books.rating')
            ->paginate(5)
            ->through(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->getTranslation('title', 'en'),
                    'author_name' => $book->author?->getTranslation('name', 'en'),
                    'category' => $book->category?->getTranslation('name', 'en'),
                    'publish_date' => $book->publish_date,
                    'star_rate' => round($book->average_rating, 2),
                    'number_of_readers' => $book->readers_count,
                ];
            });

        return response()->json($books);
    }

    public function show(int $bookId): JsonResponse
    {
        $book = Book::with([
            'author.country:id,name',
            'author:id,name,country_id',
            'category:id,name',
            'sizeCategory:id,name',
            'bookChallenges',
            'comments.reader:id,first_name,last_name,nickname,picture'
        ])->find($bookId);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404);
        }

        $averageRating = ReaderBook::where('book_id', $bookId)
            ->avg('rating') ?? 0;

        $challengeParticipants = ReaderBook::where('book_id', $bookId)
            ->where('is_challenged', true)
            ->count();

        $totalReaders = ReaderBook::where('book_id', $bookId)
            ->whereIn('status', ['in_read', 'completed'])
            ->count();

        $comments = $book->comments->map(function ($comment) {
            return [
                'image' => $comment->reader?->picture,
                'full_name' => trim($comment->reader?->first_name . ' ' . $comment->reader?->last_name),
                'nickname'  => $comment->reader?->nickname,
                'comment' => $comment->comment,
            ];
        });

        return response()->json([
            'message' => 'Book retrieved successfully.',
            'data' => [
                'title'           => $book->getTranslations('title'),
                'description'     => $book->getTranslations('description'),
                'summary'         => $book->getTranslations('summary'),
                'publish_date'    => $book->publish_date,
                'points'          => $book->points,
                'book_pdf'        => $book->book_pdf,
                'cover_image'     => $book->cover_image,
                'number_of_pages' => $book->number_of_pages,
                'author_id' => $book->author_id,
                'author_name'     => $book->author?->name,
                'category_id' => $book->category_id,
                'category_name'   => $book->Category?->name,
                'size_category_id' => $book->size_category_id,
                'size_category_name' => $book->SizeCategory?->name,
                'country_id' => $book->author?->country_id,
                'country_name' => $book->author?->country?->name,
                'rate'            => (int) $averageRating,
                'book_challenge'  => $book->bookChallenges ? [
                    'duration'    => $book->bookChallenges->duration,
                    'points'      => $book->bookChallenges->points,
                    'description' => $book->bookChallenges->description,
                ] : null,
                'book_challenge_participants' => $challengeParticipants,
                'total_readers'   => $totalReaders,
                'comments'        => $comments,
            ],
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
        ini_set('max_execution_time', 600);

        try {
            $validated = $request->validated();

            if ($request->hasFile('cover_image')) {
                $validated['cover_image'] = Cloudinary::uploadApi()->upload(
                    $request->file('cover_image')->getRealPath(),
                    ['folder' => 'reading-app/covers']
                )['secure_url'];
            }

            if ($request->hasFile('book_file')) {
                $validated['book_pdf'] = Cloudinary::uploadApi()->upload(
                    $request->file('book_file')->getRealPath(),
                    ['folder' => 'reading-app/pdfs', 'resource_type' => 'raw']
                )['secure_url'];
            }

            $book = Book::create($validated);
            $book->load('category');

            if ($book->category) {
                $followers = $book->category->readers
                    ->map(fn($r) => $r->user)
                    ->filter(fn($u) => $u);

                foreach ($followers as $user) {
                    $user->notify(new NewBookNotification($book));
                }
            }

            $title = "📚 New Book Added";
            $body  = "{$book->getTranslation('title', 'en')} is now available in {$book->category->getTranslation('name', 'en')}";

            $data = [
                'book_id' => (string) $book->id,
                'category_id' => (string) $book->category_id,
                'type' => 'info',
            ];

            try {
                $followersWithToken = $followers->filter(fn($u) => $u->fcm_token);
                $this->fcmService->notifyUsers($followersWithToken, $title, $body, $data);
            } catch (\Throwable $e) {
                Log::error("FCM push failed: " . $e->getMessage());
            }

            return response()->json([
                'message' => 'Book created successfully',
                'data' => $book
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create book',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateBookRequest $request, $id)
    {
        ini_set('max_execution_time', 600);
        try {
            $book = Book::findOrFail($id);
            $validated = $request->validated();

            if ($request->hasFile('cover_image')) {
                $validated['cover_image'] = Cloudinary::uploadApi()->upload(
                    $request->file('cover_image')->getRealPath(),
                    ['folder' => 'reading-app/covers']
                )['secure_url'];
            }

            if ($request->hasFile('book_file')) {
                $validated['book_pdf'] = Cloudinary::uploadApi()->upload(
                    $request->file('book_file')->getRealPath(),
                    ['folder' => 'reading-app/pdfs', 'resource_type' => 'raw']
                )['secure_url'];
            }

            $book->update($validated);

            return response()->json([
                'message' => 'Book updated successfully',
                'data' => $book
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Book not found'], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update book',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBookComments($bookId)
    {
        $user = Auth::user();
        $comments = Comment::with('reader')->where('book_id', '=', $bookId)->get()
            ->map(function ($comment) use ($bookId) {
                return [
                    'reader_id' => $comment->reader->id,
                    'reader_name' => $comment->reader?->first_name,
                    'reader_image' => $comment->reader?->picture,
                    'reader_nickname' => $comment->reader?->nickname,
                    'comment' => $comment->comment
                ];
            });
        return response()->json([
            'data' => $comments
        ]);
    }

    public function AddCommentToTheBook($bookId, Request $request)
    {
        $user = Auth::user();
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
            'reader_id' => $user->reader->id,
        ]);
        return response()->json([
            'message' => 'comment added successfully'
        ]);
    }

    public function searchBooks(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $books = $this->service->searchBooks($search);

        return response()->json([
            'success' => true,
            'data' => $this->service->transformBooks($books),
        ]);
    }

    public function SearchBookINCategory(Request $request, $categoryId)
    {
        $search = $request->input('search');
        $locale = app()->getLocale();

        $books = $this->service->SearchbookWithCategory($categoryId, $search);

        return response()->json([
            'success' => true,
            'data' => $this->service->transformBooks($books),
        ]);
    }
    public function search(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = Book::query();
        if ($search) {
            $query->where('title->en', 'Like', "%{$search}%");
        }
        $books = $query->get()->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->getTranslation('title', 'en'),
            ];
        });
        return response()->json([
            'books' => $books
        ]);
    }
    public function getAllBook()
    {
        $user = Auth::user();
        $books = $this->service->baseQuery()->get();
        return response()->json([
            'books' => $this->service->transformBooks($books)
        ]);
    }
    public function SarchBookWithPagination(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $query = Book::with('readers', 'readerBooks', 'author', 'category');
        if ($search) {
            $query->where('title->en', 'LIKE', "%{$search}%");
        }
        $books = $query->paginate(5)->through(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->getTranslation('title', 'en'),
                'author_name' => $book->author?->getTranslation('name', 'en'),
                'category' => $book->category?->getTranslation('name', 'en'),
                'publish_date' => $book->publish_date,
                'star_rate' => round($book->readerBooks->avg('rating') ?? 0, 2),
                'number_of_readers' => $book->readers->count(),
            ];
        });
        return response()->json([
            'books' => $books
        ]);
    }
}
