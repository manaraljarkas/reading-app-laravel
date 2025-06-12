<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookChallenges = [
            // 1. The Prophet
            [
                'book_id' => 1,
                'duration' => 5,
                'points' => 50,
                'description' => [
                    'en' => 'Reflect on life through poetry and philosophy.',
                    'ar' => 'تأمل في الحياة من خلال الشعر والفلسفة.',
                ],
            ],
            // 2. Season of Migration to the North
            [
                'book_id' => 2,
                'duration' => 14,
                'points' => 120,
                'description' => [
                    'en' => 'Explore identity and colonialism in Sudan.',
                    'ar' => 'استكشف الهوية والاستعمار في السودان.',
                ],
            ],
            // 3. Men in the Sun
            [
                'book_id' => 3,
                'duration' => 7,
                'points' => 80,
                'description' => [
                    'en' => 'Understand the Palestinian struggle through fiction.',
                    'ar' => 'افهم معاناة الفلسطينيين من خلال الأدب.',
                ],
            ],
            // 4. Palace Walk
            [
                'book_id' => 4,
                'duration' => 21,
                'points' => 160,
                'description' => [
                    'en' => 'Dive into Egyptian family life post-WWI.',
                    'ar' => 'استكشف الحياة العائلية المصرية بعد الحرب العالمية الأولى.',
                ],
            ],
            // 5. Memory in the Flesh
            [
                'book_id' => 5,
                'duration' => 18,
                'points' => 130,
                'description' => [
                    'en' => 'A poetic journey of romance and revolution.',
                    'ar' => 'رحلة شعرية بين الحب والثورة.',
                ],
            ],
            // 6. The Open Door
            [
                'book_id' => 6,
                'duration' => 15,
                'points' => 110,
                'description' => [
                    'en' => 'A feminist tale of personal liberation.',
                    'ar' => 'قصة نسوية عن التحرر الشخصي.',
                ],
            ],
            // 7. Cities of Salt
            [
                'book_id' => 7,
                'duration' => 30,
                'points' => 200,
                'description' => [
                    'en' => 'Witness the transformation of the Arab world.',
                    'ar' => 'شاهد تحولات العالم العربي.',
                ],
            ],
            // 8. Yacoubian Building
            [
                'book_id' => 8,
                'duration' => 20,
                'points' => 140,
                'description' => [
                    'en' => 'A modern look at Cairo’s urban life.',
                    'ar' => 'نظرة حديثة على حياة القاهرة الحضرية.',
                ],
            ],
            // 9. The Bamboo Stalk
            [
                'book_id' => 9,
                'duration' => 17,
                'points' => 125,
                'description' => [
                    'en' => 'Navigate identity between two cultures.',
                    'ar' => 'استكشف الهوية بين ثقافتين.',
                ],
            ],
            // 10. Frankenstein in Baghdad
            [
                'book_id' => 10,
                'duration' => 16,
                'points' => 115,
                'description' => [
                    'en' => 'Horror meets reality in war-torn Iraq.',
                    'ar' => 'الرعب يلتقي بالواقع في عراق الحرب.',
                ],
            ],
        ];

        foreach ($bookChallenges as $challenge) {
            DB::table('book_challenges')->insert([
                'book_id' => $challenge['book_id'],
                'duration' => $challenge['duration'],
                'points' => $challenge['points'],
                'description' => json_encode($challenge['description'], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
