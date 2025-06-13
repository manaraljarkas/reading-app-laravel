<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authors=[
            [
                'name' => ['en' => 'George Orwell', 'ar' => 'جورج أورويل'],
                'image' => 'george_orwell.jpg',
                'country_id' => 1,
                 'created_at' => now(),
                 'updated_at' => now(),
            ],
            [
               'name' => ['en' => 'Naguib Mahfouz', 'ar' => 'نجيب محفوظ'],
                'image' => 'naguib_mahfouz.webp',
                'country_id' => 2,
                 'created_at' => now(),
                 'updated_at' => now(),
            ],
            [
              'name' => ['en' => 'Jane Austen', 'ar' => 'جين أوستن'],
                'image' => 'jane_austen.webp',
                'country_id' => 3,
                'created_at' => now(),
                 'updated_at' => now(),
            ],
           [
              'name' => ['en' => 'Gibran Khalil Gibran', 'ar' => 'جبران خليل جبران'],
                'image' => 'gibran_khalil.webp',
                'country_id' => 3,
               'created_at' => now(),
               'updated_at' => now(),
            ],
             [
              'name' => ['en' => 'Taha Hussein', 'ar' => 'طه حسين'],
                'image' => 'taha_hussein.jpg',
                'country_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],            [
              'name' => ['en' => 'Adonis', 'ar' => 'أدونيس'],
                'image' =>  'adonis.webp',
                'country_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],            [
              'name' => ['en' => 'Mikhail Naimy', 'ar' => 'ميخائيل نعيمة'],
                'image' => 'mikhail_naimy.webp',
                'country_id' => 3,
               'created_at' => now(),
                'updated_at' => now(),
            ],            [
              'name' => ['en' => 'Ahlam Mosteghanemi', 'ar' => 'أحلام مستغانمي'],
                'image' => 'ahlam_mosteghanemi.jpg',
                'country_id' => 2,
                  'created_at' => now(),
                 'updated_at' => now(),
            ],
        ];
       foreach ($authors as $author) {
            DB::table('authors')->insert([
                'name' => json_encode($author['name'], JSON_UNESCAPED_UNICODE),
                'country_id' => $author['country_id'],
                'image' => $author['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
