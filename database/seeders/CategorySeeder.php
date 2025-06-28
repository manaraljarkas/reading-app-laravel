<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Science Fiction', 'ar' => 'خيال علمي'],
                'icon' => 'category_icon_1.jpg',
            ],
            [
                'name' => ['en' => 'Mystery', 'ar' => 'غموض'],
                'icon' => 'category_icon_2.jpg'
            ],
            [
                'name' => ['en' => 'Biography', 'ar' => 'سيرة ذاتية'],
                'icon' => 'category_icon_3.jpg'
            ],
            [
                'name' => ['en' => 'Fantasy', 'ar' => 'خيال'],
                'icon' => 'category_icon_4.jpg'
            ],
            [
                'name' => ['en' => 'History', 'ar' => 'تاريخ'],
                'icon' => 'category_icon_5.jpg'
            ],
            [
                'name' => ['en' => 'Children', 'ar' => 'أطفال'],
                'icon' => 'category_icon_6.jpg'
            ],
            [
                'name' => ['en' => 'Self-Help', 'ar' => 'تطوير الذات'],
                'icon' => 'category_icon_7.jpg'
            ],
            [
                'name' =>  ['en' => 'Religion', 'ar' => 'دين'],
                'icon' => 'category_icon_8.jpg',
            ]

        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => json_encode($category['name'], JSON_UNESCAPED_UNICODE),
                'icon' => $category['icon'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
