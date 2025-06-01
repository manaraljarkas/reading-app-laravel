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
                'number_of_books' => 9,
                'image' => 'george_orwell.jpg',
                'country_id' => 1,
                 'created_at' => now(),
                 'updated_at' => now(),
            ],
            [
               'name' => ['en' => 'Naguib Mahfouz', 'ar' => 'نجيب محفوظ'],
                'number_of_books' => 15,
                'image' => 'naguib_mahfouz.jpg',
                'country_id' => 2,
                 'created_at' => now(),
                 'updated_at' => now(),
            ],
            [
              'name' => ['en' => 'Jane Austen', 'ar' => 'جين أوستن'],
                'number_of_books' => 6,
                'image' => 'jane_austen.jpg',
                'country_id' => 3,
                'created_at' => now(),
                 'updated_at' => now(),
            ],
           [
              'name' => ['en' => 'Gibran Khalil Gibran', 'ar' => 'جبران خليل جبران'],
                'number_of_books' => 12,
                'image' => 'gibran_khalil.jpg',
                'country_id' => 3,
               'created_at' => now(),
               'updated_at' => now(),
            ],
             [
              'name' => ['en' => 'Taha Hussein', 'ar' => 'طه حسين'],
                'number_of_books' => 12,
                'image' => 'taha_hussein.jpg',
                'country_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],            [
              'name' => ['en' => 'Adonis', 'ar' => 'أدونيس'],
                'number_of_books' => 12,
                'image' =>  'adonis.jpg',
                'country_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],            [
              'name' => ['en' => 'Mikhail Naimy', 'ar' => 'ميخائيل نعيمة'],
                'number_of_books' => 12,
                'image' => 'mikhail_naimy.jpg',
                'country_id' => 3,
               'created_at' => now(),
                'updated_at' => now(),
            ],            [
              'name' => ['en' => 'Ahlam Mosteghanemi', 'ar' => 'أحلام مستغانمي'],
                'number_of_books' => 12,
                'image' => 'ahlam_mosteghanemi.jpg',
                'country_id' => 2,
                  'created_at' => now(),
                 'updated_at' => now(),
            ],
        ];
       foreach ($authors as $author) {
            DB::table('authors')->insert([
                'name' => json_encode($author['name'], JSON_UNESCAPED_UNICODE),
                'number_of_books' => $author['number_of_books'],
                'country_id' => $author['country_id'],
                'image' => $author['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    }
}
