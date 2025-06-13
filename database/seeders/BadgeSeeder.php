<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'title' => ['en' => 'First Book', 'ar' => 'أول كتاب'],
                'achievment' => ['en' => 'Completed your first book', 'ar' => 'أنهيت أول كتاب لك'],
                'image' => 'badge_1.jpg',
            ],
            [
                'title' => ['en' => '5 Books', 'ar' => '5 كتب'],
                'achievment' => ['en' => 'Completed 5 books', 'ar' => 'أنهيت 5 كتب'],
                'image' => 'badge_2.jpg',
            ],
            [
                'title' => ['en' => 'Night Reader', 'ar' => 'قارئ ليلي'],
                'achievment' => ['en' => 'Read after midnight', 'ar' => 'قرأت بعد منتصف الليل'],
                'image' => 'badge_3.jpg',
            ],
            [
                'title' => ['en' => 'Streak Master', 'ar' => 'متسلسل القراءة'],
                'achievment' => ['en' => 'Read for 7 consecutive days', 'ar' => 'قرأت لمدة 7 أيام متتالية'],
                'image' => 'badge_4.jpg',
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('badges')->insert([
                'title' => json_encode($badge['title'], JSON_UNESCAPED_UNICODE),
                'achievment' => json_encode($badge['achievment'], JSON_UNESCAPED_UNICODE),
                'image' => $badge['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
