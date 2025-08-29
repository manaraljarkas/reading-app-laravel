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
            ->first();

        if (!$readerBook) {
            $readerBook = new ReaderBook();
            $readerBook->reader_id = $reader->id;
            $readerBook->book_id = $bookId;
            $readerBook->progress = 0;
            $readerBook->status = 'in_read';
        }

        $book = Book::findOrFail($bookId);
        $progress = min($newProgress, $book->number_of_pages);
        $readerBook->progress = $progress;

        $completedNow = false;

        if ($progress >= $book->number_of_pages) {
            if ($readerBook->status !== 'completed' || is_null($readerBook->completed_at)) {
                $readerBook->completed_at = now();
            }
            $readerBook->status = 'completed';
            $completedNow = true;
        } else {
            $readerBook->status = 'in_read';
        }

        $readerBook->save();

        if ($completedNow) {
            $reader->increment('total_points', $book->points);
            app(BadgeService::class)->checkAndAward($reader, 'first_book');
            app(BadgeService::class)->checkAndAward($reader, 'points');
            if ($readerBook->is_challenged) {
                $this->bookChallengeService->handleCompletion($reader, $book);
            }
            $this->challengeService->handleBookCompletion($reader, $book);
        }
    }
}
