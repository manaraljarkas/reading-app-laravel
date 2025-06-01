<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $challenges = [
            [
                'title' => ['en' => 'Beginner Reader', 'ar' => 'قارئ مبتدئ'],
                'description' => ['en' => 'Read 2 small books in 10 days.', 'ar' => 'اقرأ كتابين صغيرين خلال 10 أيام.'],
                'points' => 50,
                'duration' => 10,
                'number_of_books' => 2,
                'size_category_id' => 1,
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => ['en' => 'Classic Challenge', 'ar' => 'تحدي الكلاسيكيات'],
                'description' => ['en' => 'Finish 3 classic novels in a month.', 'ar' => 'أنهِ 3 روايات كلاسيكية خلال شهر.'],
                'points' => 100,
                'duration' => 30,
                'number_of_books' => 3,
                'size_category_id' => 2,
                'category_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => ['en' => 'Master Reader', 'ar' => 'قارئ محترف'],
                'description' => ['en' => 'Read 5 large books in 45 days.', 'ar' => 'اقرأ 5 كتب كبيرة في 45 يوماً.'],
                'points' => 200,
                'duration' => 45,
                'number_of_books' => 5,
                'size_category_id' => 3,
                'category_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($challenges as $challenge) {
            DB::table('challenges')->insert([
                'title' => json_encode($challenge['title'], JSON_UNESCAPED_UNICODE),
                'description' => json_encode($challenge['description'], JSON_UNESCAPED_UNICODE),
                'points' => $challenge['points'],
                'duration' => $challenge['duration'],
                'number_of_books' => $challenge['number_of_books'],
                'size_category_id' => $challenge['size_category_id'],
                'category_id' => $challenge['category_id'],
                'created_at' => $challenge['created_at'],
                'updated_at' => $challenge['updated_at'],
            ]);
        }
    }

}
