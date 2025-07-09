<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $countries = [
            [
                'name' => ['en' => 'Egypt', 'ar' => 'مصر'],
                'code' => 'EG',
            ],
            [
                'name' =>['en' => 'Saudi Arabia', 'ar' => 'السعودية'],
                'code' => 'SA',
            ],
            [
                'name' => ['en' => 'United States', 'ar' => 'الولايات المتحدة'],
                'code' => 'US',
            ],
            [
                'name' =>['en' => 'France', 'ar' => 'فرنسا'],
                'code' => 'FR',
            ],
            [
                'name' =>['en' => 'Japan', 'ar' => 'اليابان'],
                'code' => 'JP',
            ],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => json_encode($country['name'], JSON_UNESCAPED_UNICODE),
                'code' => $country['code'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }}
