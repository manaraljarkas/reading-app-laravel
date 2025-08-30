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
                'name' => ['en' => 'Iraq', 'ar' => 'العراق'],
                'code' => 'IQ',
            ],
            [
                'name' => ['en' => 'Syria', 'ar' => 'سوريا'],
                'code' => 'SY',
            ],
            [
                'name' => ['en' => 'Jordan', 'ar' => 'الأردن'],
                'code' => 'JO',
            ],
            [
                'name' => ['en' => 'Lebanon', 'ar' => 'لبنان'],
                'code' => 'LB',
            ],
            [
                'name' => ['en' => 'Palestine', 'ar' => 'فلسطين'],
                'code' => 'PS',
            ],
            [
                'name' => ['en' => 'Kuwait', 'ar' => 'الكويت'],
                'code' => 'KW',
            ],
            [
                'name' => ['en' => 'Qatar', 'ar' => 'قطر'],
                'code' => 'QA',
            ],
            [
                'name' => ['en' => 'Oman', 'ar' => 'عمان'],
                'code' => 'OM',
            ],
            [
                'name' => ['en' => 'Bahrain', 'ar' => 'البحرين'],
                'code' => 'BH',
            ],
            [
                'name' => ['en' => 'Morocco', 'ar' => 'المغرب'],
                'code' => 'MA',
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
