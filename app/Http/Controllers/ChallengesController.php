<?php

namespace App\Http\Controllers;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengesController extends Controller
{
    public function getchallenges()
    {
        $readerId = Auth::id();

        $challenges = Challenge::select('challenges.id', 'challenges.title', 'description', 'points', 'challenges.created_at', 'duration', 'reader_challenges.percentage')->
        join('reader_challenges', 'challenges.id', '=', 'reader_challenges.challenge_id')
        ->where('reader_challenges.reader_id', '=', $readerId)->get();

        $now = now();

        $challenges = $challenges->map(function ($challenge) use ($readerId, $now) {
            $startDate = Carbon::parse($challenge->created_at);
            $endDate = $startDate->copy()->addDays($challenge->duration);
            $now = Carbon::now();
            $timeLeft = $now->diffInDays($endDate, false);

            $timeLeft = $timeLeft > 0 ? intval($timeLeft) : 0;

            return [
                'id' => $challenge->id,
                'title' => $challenge->title,
                'description' => $challenge->description,
                'points' => $challenge->points,
                'time_left' => $timeLeft,
                'percentage' => $challenge->percentage,
            ];
        });
        return response()->json($challenges);
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
                    'title' => $challenge->title,
                    'points' => $challenge->points,
                    'category' => $challenge->category?->name ?? 'No category',
                    'size_category' => $challenge->sizeCategory?->name,
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

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'title.en' => 'sometimes|string',
            'title.ar' => 'sometimes|string',
            'description.en' => 'sometimes|string',
            'description.ar' => 'sometimes|string',
            'points' => 'sometimes|integer',
            'duration' => 'sometimes|integer',
            'number_of_books' => 'sometimes|integer',
            'size_category_id' => 'sometimes|exists:size_categories,id',
            'category_id' => 'sometimes|exists:categories,id',
        ]);
        $challenge = Challenge::select('id', 'title', 'description', 'points', 'duration', 'number_of_books', 'size_category_id', 'category_id')->with('sizeCategory')->with('category')->findOrFail($id);
        if ($request->has('title')) {
            $challenge->title = [
                'en' => $request->input('title')['en'] ?? $challenge->title['en'],
                'ar' => $request->input('title')['ar'] ?? $challenge->title['ar'],
            ];
        }
        if ($request->has('description')) {
            $challenge->description = [
                'en' => $request->input('description')['en'] ?? $challenge->description['en'],
                'ar' => $request->input('description')['ar'] ?? $challenge->description['ar'],
            ];
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
            'title' => $challenge->title,
            'description' => $challenge->description,
            'points' => $challenge->points,
            'duration' => $challenge->duration,
            'number_of_books' => $challenge->number_of_books,
            'size_category' => $challenge->sizeCategory->name,
            'category' => $challenge->category->name,
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
                return asset('storage/' . $book->book_pdf);
            });

        return response()->json([
            'description' => $challenge->description,
            'number_of_books' => $challenge->number_of_books,
            'books_pdfs' => $books,
        ]);
    }
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title.en' => 'required|string',
            'title.ar' => 'required|string',
            'description.en' => 'required|string',
            'description.ar' => 'required|string',
            'points' => 'required|integer',
            'number_of_books' => 'required|integer',
            'duration' => 'required|integer',
            'category_id' => 'required|integer|exists:categories,id',
            'size_category_id' => 'required|integer|exists:size_categories,id',
            'ids_books' => 'sometimes|array',
            'ids_books.*' => 'integer|exists:books,id'
        ]);

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

    if ($request->has('ids_books')) {
        foreach ($request->ids_books as $book_id) {
            DB::table('challenge_books')->insert([
                'challenge_id' => $challenge->id,
                'book_id' => $book_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
});
}}



