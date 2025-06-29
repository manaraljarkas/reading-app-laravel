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
                'longitude' => 30.802498,
                'latitude' => 26.820553,
            ],
            [
                'name' =>['en' => 'Saudi Arabia', 'ar' => 'السعودية'],
                'code' => 'SA',
                'longitude' => 45.0792,
                'latitude' => 23.8859,
            ],
            [
                'name' => ['en' => 'United States', 'ar' => 'الولايات المتحدة'],
                'code' => 'US',
                'longitude' => -95.7129,
                'latitude' => 37.0902,
            ],
            [
                'name' =>['en' => 'France', 'ar' => 'فرنسا'],
                'code' => 'FR',
                'longitude' => 2.2137,
                'latitude' => 46.2276,
            ],
            [
                'name' =>['en' => 'Japan', 'ar' => 'اليابان'],
                'code' => 'JP',
                'longitude' => 138.2529,
                'latitude' => 36.2048,
            ],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => json_encode($country['name'], JSON_UNESCAPED_UNICODE),
                'code' => $country['code'],
                'longitude' => $country['longitude'],
                'latitude' => $country['latitude'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }}
