<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['en' => 'Science Fiction', 'ar' => 'خيال علمي'],
            ['en' => 'Mystery', 'ar' => 'غموض'],
            ['en' => 'Biography', 'ar' => 'سيرة ذاتية'],
            ['en' => 'Fantasy', 'ar' => 'خيال'],
            ['en' => 'History', 'ar' => 'تاريخ'],
            ['en' => 'Children', 'ar' => 'أطفال'],
            ['en' => 'Self-Help', 'ar' => 'تطوير الذات'],
            ['en' => 'Religion', 'ar' => 'دين'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => json_encode($category, JSON_UNESCAPED_UNICODE),
                'icon' => Str::slug($category['en']) . '.svg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
