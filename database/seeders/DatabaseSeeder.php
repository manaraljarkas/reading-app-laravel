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
        $this->call(CountrySeeder::class);
        $this->call(AuthorSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SizeCategorySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ReaderSeeder::class);
        $this->call(BookSeeder::class);
        $this->call(BadgeSeeder::class);
        $this->call(BookChallengeSeeder::class);
        $this->call(BookSuggestionSeeder::class);
        $this->call(ChallengeSeeder::class);
        $this->call(CommentSeeder::class);
        $this->call(ComplaintSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
