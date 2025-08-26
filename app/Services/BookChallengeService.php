<?php

namespace App\Services;

use App\Models\{Reader, Book, BookChallenge, ReaderBook};
use Carbon\Carbon;

class BookChallengeService
{
    public function handleCompletion(Reader $reader, Book $book)
    {
        $challenge = BookChallenge::where('book_id', $book->id)->first();
        if (!$challenge) {
            return;
        }

        $readerBook = ReaderBook::where('reader_id', $reader->id)
            ->where('book_id', $book->id)
            ->first();

        if (!$readerBook || !$readerBook->is_challenged || !$readerBook->challenge_joined_at) {
            return; // not part of the challenge or missing join date
        }

        $joinedAt = $readerBook->challenge_joined_at;
        $deadline = \Carbon\Carbon::parse($joinedAt)->addDays($challenge->duration);

        if (now()->lte($deadline)) {
            $reader->increment('total_points', $challenge->points);
        }

        // Always reset challenge state (success or fail) so reader can rejoin
        $readerBook->update([
            'is_challenged' => false,
            'challenge_joined_at' => null,
        ]);
    }
}
