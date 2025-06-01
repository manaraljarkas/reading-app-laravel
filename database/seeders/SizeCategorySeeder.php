<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SizeCategory;
class SizeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $sizes=[
          ['ar' => 'صغير',  'en' => 'Small'],
          ['ar' => 'متوسط', 'en' => 'Medium'],
          ['ar' => 'كبير',  'en' => 'Large'],
       ];

       foreach($sizes as $size){
        SizeCategory::create([
        'name' => json_encode($size, JSON_UNESCAPED_UNICODE)
        ]);
       }

    }
   }
