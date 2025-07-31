<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReadingProgressRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\ReaderBook;
use App\Models\Book;
use App\Models\Reader;
use Illuminate\Http\JsonResponse;
use App\Services\BookService;
use Illuminate\Http\Request;

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

    public function updateReadingProgress(UpdateReadingProgressRequest $request, $id)
    {
        $userId = Auth::id();

        $reader = Reader::where('user_id', $userId)->first();

        if (!$reader) {
            return response()->json([
                'message' => 'Reader profile not found.',
            ], 404);
        }

        $readerBook = ReaderBook::where('reader_id', $reader->id)
            ->where('book_id', $id)
            ->first();

        if (!$readerBook) {
            return response()->json([
                'message' => 'Reading progress not found for this book.',
            ], 404);
        }

        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found.',
            ], 404);
        }

        $progress = min($request->progress, $book->number_of_pages);
        $readerBook->progress = $progress;

        if ($progress >= $book->number_of_pages) {
            $readerBook->status = 'completed';
        } elseif ($readerBook->status === 'completed' && $progress < $book->number_of_pages) {
            $readerBook->status = 'in_read';
        }

        $readerBook->save();

        return response()->json([
            'message' => 'Reading progress updated successfully.'
        ]);
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
}
