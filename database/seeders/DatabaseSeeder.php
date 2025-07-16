<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
            ComplaintSeeder::class,
            SuperAdminPermissionSeeder::class
        ]);
    }
}
