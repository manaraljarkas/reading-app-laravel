<?php

namespace Database\Seeders;
use App\Models\SizeCategory;
use App\Models\Country;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            CountrySeeder::class,
            AuthorSeeder::class,
            CategorySeeder::class,
            SizeCategorySeeder::class,
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            ReaderSeeder::class,
            BookSeeder::class,
            BadgeSeeder::class,
            BookChallengeSeeder::class,
            BookSuggestionSeeder::class,
            ChallengeSeeder::class,
            CommentSeeder::class,
            ComplaintSeeder::class
        ]);
    }
}
