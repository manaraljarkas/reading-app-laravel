<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Reader;
use App\Notifications\BadgeUnlockedNotification;
use Illuminate\Support\Facades\Log;

class BadgeService
{
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function checkAndAward(Reader $reader, string $eventType): void
    {
        $badges = Badge::where('type', $eventType)->get();

        foreach ($badges as $badge) {
            $earned = $this->isEarned($reader, $badge);

            if (!$earned) {
                continue;
            }

            $alreadyHas = $reader->badges()->where('badge_id', $badge->id)->exists();

            $canAward = match ($badge->type) {
                'first_book' => !$alreadyHas,                     // only once
                'points' => true,                                // every time
                'challenge_completed' => true,                   // every time
                default => !$alreadyHas,
            };

            if ($canAward) {
                $reader->badges()->attach($badge->id);

                if ($reader->user) {
                    $reader->user->notify(new BadgeUnlockedNotification($badge));

                    try {
                        if ($reader->user->fcm_token) {
                            $title = method_exists($badge, 'getTranslation')
                                ? $badge->getTranslation('title', 'en')
                                : $badge->title;

                            $body = method_exists($badge, 'getTranslation')
                                ? $badge->getTranslation('achievement', 'en')
                                : $badge->achievement;

                            $data = [
                                'badge_id' => (string) $badge->id,
                                'type'     => 'badge',
                                'image'    => $badge->image,
                            ];

                            $this->fcmService->notifyUsers(
                                collect([$reader->user]),
                                "🏆 {$title}",
                                $body,
                                $data
                            );
                        }
                    } catch (\Throwable $e) {
                        Log::error('Badge FCM push failed: ' . $e->getMessage());
                    }
                }
            }
        }
    }


    protected function isEarned(Reader $reader, Badge $badge): bool
    {
        return match ($badge->type) {
            'first_book' => $reader->books()
                ->wherePivot('status', 'completed')
                ->count() >= 1,

            'points' => $reader->total_points >= ($badge->threshold ?? 0),

            'challenge_completed' => $reader->challenges()
                ->wherePivot('progress', 'completed')
                ->count() >= 1,

            default => false,
        };
    }
}
