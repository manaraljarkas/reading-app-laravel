<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChallengeRequest;
use App\Http\Requests\UpdateChallengeRequest;
use App\Models\Book;
use App\Models\BookChallenge;
use App\Models\Challenge;
use App\Models\ReaderBook;
use App\Models\ReaderChallenge;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengesController extends Controller
{
    protected BookService $service;

    public function __construct(BookService $service)
    {
        $this->service = $service;
    }
    public function getchallenges()
    {
        $user = Auth::user();
        $readerId = $user->reader?->id;
        if (!$readerId) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }
        $challenges = Challenge::select('challenges.id', 'challenges.title', 'description', 'points', 'challenges.created_at', 'duration', 'reader_challenges.percentage')->join('reader_challenges', 'challenges.id', '=', 'reader_challenges.challenge_id')
            ->where('reader_challenges.reader_id', '=', $readerId)->get();

        $now = now();

        $challenges = $challenges->map(function ($challenge) use ($readerId, $now) {
            $locale = app()->getLocale();
            $startDate = Carbon::parse($challenge->created_at);
            $endDate = $startDate->copy()->addDays($challenge->duration);
            $now = Carbon::now();
            $timeLeft = $now->diffInDays($endDate, false);

            $timeLeft = $timeLeft > 0 ? intval($timeLeft) : 0;

            return [
                'id' => $challenge->id,
                'title' => $challenge->getTranslation('title', $locale),
                'description' => $challenge->getTranslation('description', $locale),
                'points' => $challenge->points,
                'time_left' => $timeLeft,
                'percentage' => $challenge->percentage,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $challenges
        ]);
    }

    public function index()
    {
        $user = Auth::user();

        $challenges = Challenge::with('category')
            ->with('sizeCategory')
            ->withCount('readers')
            ->paginate(5)
            ->through(function ($challenge) {
                return [
                    'id' => $challenge->id,
                    'title' => $challenge->getTranslation('title', 'en'),
                    'points' => $challenge->points,
                    'category' => $challenge->category?->getTranslation('name', 'en') ?? 'No category',
                    'size_category' => $challenge->sizeCategory?->getTranslation('name', 'en'),
                    'duration' => $challenge->duration,
                    'number_of_participants' => $challenge->readers_count,
                ];
            });

        return response()->json($challenges);
    }

    public function destroy($challengeId)
    {
        $user = Auth::user();
        $challenge = Challenge::find($challengeId);
        if (!$challenge) {
            return response()->json(
                [
                    'message' => 'Challenge not found',
                ],
                404,
            );
        }
        $challenge->delete();
        return response()->json([
            'message' => 'Challenge deleted successfully',
        ]);
    }

    public function update(UpdateChallengeRequest $request, $id)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $challenge = Challenge::findOrFail($id);

        if (empty($validated)) {
            return response()->json([
                'message' => 'No update data provided.'
            ], 422);
        }

        $challenge->update($validated);

        $challenge->load(['sizeCategory', 'category']);
        return response()->json([
            'succes' => true,
            'message' => 'Challenge Updated Successful.',
            'data' => [
                'title' => $challenge->getTranslations('title'),
                'description' => $challenge->getTranslations('description'),
                'points' => $challenge->points,
                'duration' => $challenge->duration,
                'number_of_books' => $challenge->number_of_books,
                'size_category' => $challenge->sizeCategory?->getTranslations('name'),
                'category' => $challenge->category?->getTranslations('name'),
            ]
        ]);
    }
    public function show($id)
    {
        $challenge = Challenge::findOrFail($id);

        $books = DB::table('challenge_books')
            ->where('challenge_id', $id)
            ->join('books', 'books.id', '=', 'challenge_books.book_id')
            ->select('books.book_pdf')
            ->get()
            ->map(function ($book) {
                return  $book->book_pdf;
            });

        return response()->json([
            'success' => true,
            'message' => 'Challenge details retrieved successfully.',
            'data' => [
                'description' => $challenge->getTranslations('description'),
                'number_of_books' => $challenge->number_of_books,
                'books_pdfs' => $books,
            ]
        ]);
    }
    public function store(StoreChallengeRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $challenge = Challenge::create([
                'title' => [
                    'en' => $data['title']['en'],
                    'ar' => $data['title']['ar']
                ],
                'description' => [
                    'en' => $data['description']['en'],
                    'ar' => $data['description']['ar']
                ],
                'points' => $data['points'],
                'number_of_books' => $data['number_of_books'],
                'duration' => $data['duration'],
                'category_id' => $data['category_id'],
                'size_category_id' => $data['size_category_id'],
            ]);

            if (!empty($data['ids_books'])) {
                if (count($data['ids_books']) > $data['number_of_books']) {
                    abort(400, 'The number of entered books is more than the number of challenge books.');
                }

                foreach ($data['ids_books'] as $book_id) {
                    DB::table('challenge_books')->insert([
                        'challenge_id' => $challenge->id,
                        'book_id' => $book_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Challenge added successfully',
            'data' => $data
        ]);
    }

    public function GetBookChallenge($bookId)
    {
        $user = Auth::user();
        $locale = app()->getLocale();
        $book_challenge = BookChallenge::findOrFail($bookId);

        return response()->json([
            'id' => $book_challenge->id,
            'description' => $book_challenge->getTranslation('description', $locale),
            'points' => $book_challenge->points,
            'duration' => $book_challenge->duration
        ]);
    }

    public function JoinToBookChallenge($bookId)
    {
        $user = Auth::user();
        $reader = $user->reader;
        if (!$reader) {
            return response()->json(['message' => 'Reader not found.'], 404);
        }
        $book = Book::findOrFail($bookId);
        if (!$book) {
            return response()->json(['message' => 'Book not found.'], 404);
        }
        $book_challenge = BookChallenge::where('book_id', $bookId)->first();

        if (!$book_challenge) {
            return response()->json(['message' => 'No challenge found for this book.'], 404);
        }

        $already = ReaderBook::where('book_id', $bookId)->where('reader_id', $reader->id)
            ->where('is_challenged', true)->exists();

        if ($already) {
            return response()->json(['message' => 'Already joined this book challenge.'], 409);
        }
        $reader->books()->syncWithoutDetaching([
            $bookId => [
                'is_challenged' => true,
                'status' => 'in_read',
                'challenge_joined_at' => now(),
            ]
        ]);

        return response()->json(['message' => 'Joined to challenge successfully.']);
    }
    public function getAllChallenges()
    {
        $user = Auth::user();
        $challenges = Challenge::with('sizeCategory', 'category', 'books', 'books.readerBooks', 'readers')->orderByDesc('created_at')->get()->map(function ($challenge) use ($user) {
         $is_challenged = $challenge->readers->contains('id', $user->reader->id);

            return [
                'id' => $challenge->id,
                'title' => $challenge->title,
                'description' => $challenge->description,
                'points' => $challenge->points,
                'duration' => $challenge->duration,
                'number_of_books' => $challenge->number_of_books,
                'size_category_name' => $challenge->sizeCategory->name,
                'is_challenged' => $is_challenged,
                'category' => [
                    'id' => $challenge->category->id,
                    'name' => $challenge->category->name,
                    'icon' => $challenge->category->icon
                ],
                'books' =>$this->service->transformBooks($challenge->books),

            ];
        });
        return response()->json(['data' => $challenges]);
    }

    public function JoinToChallenge($challengeId)
    {
        $user = Auth::user();
        $reader = $user->reader;

        // Make sure challenge exists
        $challenge = Challenge::findOrFail($challengeId);

        return DB::transaction(function () use ($reader, $challenge) {

            // 1) Hard check the attempts table directly (ignore soft-deleted)
            $activeExists = ReaderChallenge::where('challenge_id', $challenge->id)
                ->where('reader_id', $reader->id)
                ->where('progress', 'in_progress')
                ->whereNull('deleted_at')
                ->lockForUpdate() // prevent race: two requests joining at the same time
                ->exists();

            if ($activeExists) {
                return response()->json([
                    'message' => 'You are already participating in this challenge.'
                ], 409);
            }

            // 2) Create a brand-new attempt row
            ReaderChallenge::create([
                'challenge_id'    => $challenge->id,
                'reader_id'       => $reader->id,
                'progress'        => 'in_progress',
                'percentage'      => 0,
                'completed_books' => 0,   // ensure this column exists; if not, remove this line
            ]);

            return response()->json([
                'message' => 'Joined to challenge successfully.'
            ]);
        });
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $query = Challenge::with(['category', 'sizeCategory'])
            ->withCount('readers');

        if ($search) {
            $query->where('title->en', 'LIKE', "%{$search}%");
        }

        $challenges = $query->paginate(5)->through(function ($challenge) {
            return [
                'id' => $challenge->id,
                'title' => $challenge->getTranslation('title', 'en'),
                'points' => $challenge->points,
                'category' => $challenge->category?->getTranslation('name', 'en') ?? 'No category',
                'size_category' => $challenge->sizeCategory?->getTranslation('name', 'en'),
                'duration' => $challenge->duration,
                'number_of_participants' => $challenge->readers_count,
            ];
        });

        return response()->json($challenges);
    }

    public function getSuccessChallenge()
    {
        $user = Auth::user();
        $Challenges = ReaderChallenge::where('progress', 'completed')->select('id','progress','percentage','completed_books','challenge_id','reader_id')->get();
        return response()->json([
            'challenges' => $Challenges
        ]);
    }
}
