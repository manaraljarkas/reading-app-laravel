<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizecategories = [
            [
                'name' => ['ar' => 'صغير',  'en' => 'Small']
            ],
            [
                'name' => ['ar' => 'متوسط', 'en' => 'Medium']
            ],
            [
                'name' => ['ar' => 'كبير',  'en' => 'Large']
            ]
        ];

        foreach ($sizecategories as $sizecategory) {
            DB::table('size_categories')->insert([
                'name' => json_encode($sizecategory['name'], JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
