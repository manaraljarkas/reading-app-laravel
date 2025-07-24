<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChallengeRequest;
use App\Http\Requests\UpdateChallengeRequest;
use App\Models\Book;
use App\Models\BookChallenge;
use App\Models\Challenge;
use App\Models\ReaderBook;
use App\Models\ReaderChallenge;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengesController extends Controller
{
    public function getchallenges()
    {
        $user = Auth::user();
        $readerId = $user->reader?->id;
        if (!$readerId) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }
        $challenges = Challenge::select('challenges.id', 'challenges.title', 'description', 'points', 'challenges.created_at', 'duration', 'reader_challenges.percentage')->
        join('reader_challenges', 'challenges.id', '=', 'reader_challenges.challenge_id')
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
        'success'=>true,
        'data'=>$challenges
        ]);
    }

    public function index()
    {
        $user = Auth::user();

        $challenges = Challenge::with('category')
            ->with('sizeCategory')
            ->withCount('readers')
            ->paginate(10)
            ->through(function ($challenge) {
                return [
                    'id' => $challenge->id,
                    'title' => $challenge->getTranslations('title'),
                    'points' => $challenge->points,
                    'category' => $challenge->category?->getTranslations('name') ?? 'No category',
                    'size_category' => $challenge->sizeCategory?->getTranslations('name'),
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

        $challenge = Challenge::select('id', 'title', 'description', 'points', 'duration', 'number_of_books', 'size_category_id', 'category_id')->with('sizeCategory')->with('category')->findOrFail($id);
        if ($request->has('title.en')) {
            $challenge->setTranslation('title', 'en', $request->input('title.en'));
        }
        if ($request->has('title.ar')) {
            $challenge->setTranslation('title', 'ar', $request->input('title.ar'));
        }

        if ($request->has('description.en')) {
            $challenge->setTranslation('description', 'en', $request->input('description.en'));
        }
        if ($request->has('description.ar')) {
            $challenge->setTranslation('description', 'ar', $request->input('description.ar'));
        }
        if ($request->has('points')) {
            $challenge->points = $request->input('points');
        }
        if ($request->has('duration')) {
            $challenge->duration = $request->input('duration');
        }
        if ($request->has('number_of_books')) {
            $challenge->number_of_books = $request->input('number_of_books');
        }
        if ($request->has('size_category_id')) {
            $challenge->size_category_id = $request->size_category_id;
        }
        if ($request->has('category_id')) {
            $challenge->category_id = $request->category_id;
        }
        $challenge->load('sizeCategory');
        $challenge->load('category');
        $challenge->save();
        return response()->json([
            'title' => $challenge->getTranslations('title'),
            'description' => $challenge->getTranslations('description'),
            'points' => $challenge->points,
            'duration' => $challenge->duration,
            'number_of_books' => $challenge->number_of_books,
            'size_category' => $challenge->sizeCategory->getTranslations('name'),
            'category' => $challenge->category->getTranslations('name'),
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
            'description' => $challenge->getTranslations('description'),
            'number_of_books' => $challenge->number_of_books,
            'books_pdfs' => $books,
        ]);
    }
    public function store(StoreChallengeRequest $request)
    {
        $user = Auth::user();

        DB::transaction(function () use ($request) {
            $challenge = Challenge::create([
                'title' => [
                    'en' => $request->input('title')['en'],
                    'ar' => $request->input('title')['ar']
                ],
                'description' => [
                    'en' => $request->input('description')['en'],
                    'ar' => $request->input('description')['ar']
                ],
                'points' => $request->points,
                'number_of_books' => $request->number_of_books,
                'duration' => $request->duration,
                'category_id' => $request->category_id,
                'size_category_id' => $request->size_category_id,
            ]);
            $number_of_books = $request->number_of_books;
            $ids_books = $request->ids_books;

            if ($request->has('ids_books')) {
                $bookIds = $request->ids_books;

                if (count($bookIds) > $number_of_books) {
                    abort(400, 'The number of entered books is more than the number of challenge books.');
                }

                foreach ($bookIds as $book_id) {
                    DB::table('challenge_books')->insert([
                        'challenge_id' => $challenge->id,
                        'book_id' => $book_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
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

        $already=ReaderBook::where('book_id',$bookId)->where('reader_id',$reader->id)
         ->where('is_challenged', true)->exists();

        if($already){
         return response()->json(['message' => 'Already joined this book challenge.'], 409);
        }
        $reader->books()->syncWithoutDetaching([
            $bookId => [
                'is_challenged' => true,
                'status' => 'in_read',
            ]
        ]);

        return response()->json(['message' => 'Joined to challenge successfully.']);
    }
}
