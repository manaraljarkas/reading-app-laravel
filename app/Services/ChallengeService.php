<?php

namespace App\Services;

use App\Models\{Book, Reader, Challenge, ReaderChallenge};
use Carbon\Carbon;

class ChallengeService
{
    public function handleBookCompletion(Reader $reader, Book $book = null)
    {
        $readerChallenges = ReaderChallenge::where('reader_id', $reader->id)
            ->where('progress', 'in_progress')
            ->get();

        foreach ($readerChallenges as $rc) {
            $challenge = Challenge::find($rc->challenge_id);
            if (!$challenge) continue;

            $deadline = Carbon::parse($rc->created_at)->addDays($challenge->duration);

            if (now()->gt($deadline)) {
                $rc->update([
                    'progress' => 'failed',
                    'percentage' => 0,
                    'completed_books' => 0
                ]);
                continue;
            }

            $query = $reader->books()
                ->wherePivot('status', 'completed')
                ->wherePivot('completed_at', '>=', $rc->created_at);

            if ($challenge->books()->exists()) {
                $query->whereIn('books.id', $challenge->books->pluck('book_id'));
            }

            $completedCount = $query->count();
            $percentage = ($completedCount / $challenge->number_of_books) * 100;

            $rc->update([
                'percentage' => $percentage,
                'completed_books' => $completedCount
            ]);

            if ($percentage >= 100) {
                $rc->update(['progress' => 'completed']);
                $reader->increment('total_points', $challenge->points);
            }
        }
    }
}
