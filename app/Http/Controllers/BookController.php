<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Badge;
use App\Models\Book;
use App\Models\BookChallenge;
use App\Models\Category;
use App\Models\Challenge;
use App\Models\Comment;
use App\Models\Reader;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class BookController extends Controller
{
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
                    'title' => $book->getTranslation('title','en'),
                    'author_name' => $book->author?->getTranslation('name','en'),
                    'category' => $book->category?->getTranslation('name','en'),
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
        $book_challenge_points = DB::table('reader_books')
            ->where('book_id', $bookId)
            ->where('reader_id', $user->reader->id)
            ->value('earned_points');

        $comments = Comment::where('book_id', '=', $bookId)
            ->with('reader')
            ->get()
            ->map(function ($comment) {
                return [
                    'image' => $comment->reader?->picture,
                    'name' => $comment->reader?->first_name,
                    'comment' => $comment->comment,
                ];
            });

        return response()->json([
            'size_Category' => $book->sizecategory->getTranslations('name'),
            'description' => $book->getTranslations('description'),
            'summary' => $book->getTranslations('summary'),
            'book_challenge_duration' => $book->bookChallenges?->duration,
            'book_challenge_points' => $book_challenge_points ,
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
        ini_set('max_execution_time', 360);

        DB::transaction(function () use ($request) {

            $coverUpload = Cloudinary::uploadApi()->upload(
                $request->file('cover_image')->getRealPath(),
                ['folder' => 'reading-app/covers']
            );
            $coverUrl = $coverUpload['secure_url'];

            $pdfUpload = Cloudinary::uploadApi()->upload(
                $request->file('book_file')->getRealPath(),
                ['folder' => 'reading-app/pdfs', 'resource_type' => 'raw']
            );
            $pdfUrl = $pdfUpload['secure_url'];

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
                'book_pdf' => $pdfUrl,
                'cover_image' => $coverUrl,
                'size_category_id' => $request->size_category_id,
                'category_id' => $request->category_id,
                'points'=>$request->points,
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
        return response()->json([
            'success' => true,
            'message' => 'Book added successfully'
        ]);
    }

    public function update(UpdateBookRequest $request, string $id): JsonResponse
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Book not found.'], 404);
        }

        try {
            DB::transaction(function () use ($book, $request) {
                $data = $request->only([
                    'publish_date',
                    'number_of_pages',
                    'category_id',
                    'size_category_id',
                    'author_id',
                    'points'
                ]);

                if ($request->hasFile('cover_image')) {
                    $upload = Cloudinary::uploadApi()->upload(
                        $request->file('cover_image')->getRealPath(),
                        ['folder' => 'reading-app/covers']
                    );
                    $data['cover_image'] = $upload['secure_url'];
                }

                if ($request->hasFile('book_file')) {
                    $upload = Cloudinary::uploadApi()->upload(
                        $request->file('book_file')->getRealPath(),
                        ['folder' => 'reading-app/pdfs', 'resource_type' => 'raw']
                    );
                    $data['book_pdf'] = $upload['secure_url'];
                }

                // Just merge translated fields as JSON
                foreach (['title', 'description', 'summary'] as $field) {
                    if ($request->has($field)) {
                        $book->$field = array_merge(
                            (array) $book->$field,
                            (array) $request->input($field)
                        );
                    }
                }

                $book->fill($data)->save();

                // Update or create related challenge
                if (
                    $request->filled('challenge_duration') ||
                    $request->filled('challenge_points') ||
                    $request->has('description_BookChallenge')
                ) {
                    $challengeData = [];

                    if ($request->filled('challenge_duration')) {
                        $challengeData['duration'] = $request->challenge_duration;
                    }

                    if ($request->filled('challenge_points')) {
                        $challengeData['points'] = $request->challenge_points;
                    }

                    if ($request->has('description_BookChallenge')) {
                        $challengeData['description'] = array_merge(
                            (array) optional($book->bookChallenges)->description,
                            (array) $request->input('description_BookChallenge')
                        );
                    }

                    $book->bookChallenges()->updateOrCreate(
                        ['book_id' => $book->id],
                        $challengeData
                    );
                }
            });

            return response()->json([
                'message' => 'Book updated successfully.',
                'data' => $book->refresh()
            ]);
        } catch (\Throwable $e) {
            Log::error('Book update failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'Failed to update book.',
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
                    'reader_name' => $comment->reader?->first_name,
                    'reader_image' => $comment->reader?->picture,
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
}
