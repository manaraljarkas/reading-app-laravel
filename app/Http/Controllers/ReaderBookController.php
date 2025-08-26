<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReadingProgressRequest;
use App\Models\Book;
use App\Models\Reader;
use App\Models\ReaderBook;
use App\Services\{BookService, ReadingProgressService};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReaderBookController extends Controller
{
    protected BookService $service;

    public function __construct(BookService $service)
    {
        $this->service = $service;
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
        $is_listed = ReaderBook::where('book_id', $bookId)->where('reader_id', $reader->id)->where('is_listed', true)->first();
        if ($is_listed) {
            $is_listed->update([
                'is_listed' => false
            ]);
            return response()->json([
                'message' => 'Book removed to To-Do List',
                'is_listed' => false,
            ]);
        } else {
            $reader->books()->syncWithoutDetaching([
                $bookId => ['is_listed' => true]
            ]);
            return response()->json([
                'message' => 'Book added to To-Do List',
                'is_listed' => true
            ]);
        }
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

    public function updateReadingProgress(UpdateReadingProgressRequest $request, $id)
    {
        $userId = Auth::id();
        $reader = Reader::where('user_id', $userId)->first();

        if (!$reader) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }

        try {
            app(ReadingProgressService::class)->updateProgress($reader, $id, $request->progress);
            return response()->json(['message' => 'Reading progress updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function removeFromFavorites($bookId)
    {
        $user = Auth::user();
        $reader = $user->reader;

        if (!$reader) {
            return response()->json(['message' => 'Reader profile not found.'], 404);
        }

        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['message' => 'Book not found.'], 404);
        }

        $readerBook = $reader->books()->where('book_id', $bookId)->first();

        if (!$readerBook) {
            return response()->json(['message' => 'Book is not in your reading list.'], 404);
        }

        $reader->books()->updateExistingPivot($bookId, ['is_favourite' => false]);

        return response()->json(['message' => 'Book removed from favorites successfully.']);
    }

    public function getReaderBookInfo()
    {
        $user = Auth::user();
        $readerId = $user->reader->id;

        $CountService = new \App\Services\BookService();
        $CountBook = new \App\Services\CountService($readerId);

        $average_rating = (float) $CountService->CalculatingTheAverage();
        $sum_books = $CountBook->countBooks() + $CountBook->Number_of_books_in_favorites();
        return response()->json([
            'average_rating' => $average_rating,
            'sum_books' => $sum_books,
        ]);
    }
}
