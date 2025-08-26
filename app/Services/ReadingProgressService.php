<?php

namespace App\Services;

use App\Models\{Reader, ReaderBook, Book};

class ReadingProgressService
{
    protected BookChallengeService $bookChallengeService;
    protected ChallengeService $challengeService;

    public function __construct(BookChallengeService $bookChallengeService, ChallengeService $challengeService)
    {
        $this->bookChallengeService = $bookChallengeService;
        $this->challengeService = $challengeService;
    }

    public function updateProgress(Reader $reader, int $bookId, int $newProgress)
    {
        $readerBook = ReaderBook::where('reader_id', $reader->id)
            ->where('book_id', $bookId)
            ->firstOrFail();

        $book = Book::findOrFail($bookId);

        // Update progress
        $progress = min($newProgress, $book->number_of_pages);
        $readerBook->progress = $progress;

        $completedNow = false;

        if ($progress >= $book->number_of_pages) {
            if ($readerBook->status !== 'completed' || is_null($readerBook->completed_at)) {
                $readerBook->completed_at = now();
            }
            $readerBook->status = 'completed';
            $completedNow = true;
        } elseif ($progress < $book->number_of_pages) {
            $readerBook->status = 'in_read';
        }

        $readerBook->save();

        // --- If completed: award points & check challenges
        if ($completedNow) {
            $reader->increment('total_points', $book->points);

            // Book-specific challenge
            if ($readerBook->is_challenged) {
                $this->bookChallengeService->handleCompletion($reader, $book);
            }

            // Multi-book challenges
            $this->challengeService->handleBookCompletion($reader, $book);
        }
    }
}
