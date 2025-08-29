<?php
namespace App\Services;

use App\Models\Badge;
use App\Models\Reader;

class BadgeService
{
    public function checkAndAward(Reader $reader, string $eventType): void
    {
        $badges = Badge::where('type', $eventType)->get();

        foreach ($badges as $badge) {
            if ($reader->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }

            $earned = match ($eventType) {
                'first_book' => $reader->books()
                    ->wherePivot('status', 'completed')->count() > 0,

                'points' => true,

                'challenge_completed' => $reader->challenges()
                    ->wherePivot('progress', 'completed')
                    ->count() > 0,

                default => false,
            };

            if ($earned) {
                $reader->badges()->attach($badge->id);
            }
        }
    }
}
