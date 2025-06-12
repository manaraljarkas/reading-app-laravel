<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSuggestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suggestions = [
            ['title' => 'The Midnight Library', 'author_name' => 'Matt Haig', 'note' => 'A friend recommended this for its perspective on choices.'],
            ['title' => 'Can’t Hurt Me', 'author_name' => 'David Goggins', 'note' => 'Seems like a powerful motivational book.'],
            ['title' => 'Dune', 'author_name' => 'Frank Herbert', 'note' => 'Classic sci-fi that I’ve always wanted to read.'],
            ['title' => 'Becoming', 'author_name' => 'Michelle Obama', 'note' => 'I’m inspired by her story.'],
            ['title' => 'The Four Agreements', 'author_name' => 'Don Miguel Ruiz', 'note' => 'Looks like a short but deep read.'],
            ['title' => 'The Book Thief', 'author_name' => 'Markus Zusak', 'note' => 'Heard it’s emotional and beautifully written.'],
        ];

        $readerIds = DB::table('readers')->pluck('id');

        foreach ($readerIds as $index => $readerId) {
            $suggestion = $suggestions[$index % count($suggestions)];

            DB::table('book_suggestions')->insert([
                'title'       => $suggestion['title'],
                'author_name' => $suggestion['author_name'],
                'note'        => $suggestion['note'],
                'reader_id'   => $readerId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
